const TableEditor = {
    // -------- PUBLIC --------
    init: function(id) {
        const val = this._loadValue(id);

        // setup columns
        for (const col of val.s) {
            this._addVisualColumn(id, col.l);
        }

        this._addVisualColumn(id, 'Действия');

        // setup rows
        let i = 0;
        for (const row of val.v) {
            this._addVisualRow(id, i++, row);
        }
    },

    onRowEdit: function (id, r) {
        // compose input crap
        const val = this._loadValue(id);
        let input = '';
        for (const col of val.s) {
            const cv = r != null ? val.v[r][col.n] : col.v;
            if (col.t.startsWith('editor')) {
                input += `
                <div class="form-group">
                    <label for="te-${col.n}">${col.l}</label>
                    <textarea class="form-control" name="${col.n}" id="te-${col.n}" placeholder="${col.l}" rows="5">${cv}</textarea>
                </div>`;
            }
            else if (col.t.startsWith('image')) {
                input += `
<div class="form-group">
    <label for="te-${col.n}">${col.l}</label>
    <img src="${cv}" alt="${col.l}" id="te-${col.n}-img" style="max-width: 50% !important;">
    <input type="file" data-val="${cv}" class="form-control" name="${col.n}" id="te-${col.n}" onchange="TableEditor.onUpdateImage('${id}', '${col.n}', this)">
</div>
                `;
            }
            else if (col.t.startsWith('file')) {
                input += `
<div class="form-group">
    <label for="te-${col.n}">${col.l}</label>
    <p id="te-${col.n}-file">${cv}</p>
    <input type="file" data-val="${cv}" class="form-control" name="${col.n}" id="te-${col.n}" onchange="TableEditor.onUpdateFile('${id}', '${col.n}', this)">
</div>
                `;
            }
            else if (col.t.startsWith('combo')) {
                input += `<div class="form-group">
    <label for="te-${col.n}">${col.l}</label>`;

                const params = col.e.split(',,');
                input += `<select class="form-control" id="te-${col.n}" name="${col.n}">`;
                for (const param of params) {
                    const par = param.split('=');
                    input += `<option ${cv === par[0] ? 'selected' : ''} value="${par[0]}">${par[1]}</option>`;
                }
                input += '</select>';
                input += '</div>';
            } else {
                input += `
<div class="form-group">
    <label for="te-${col.n}">${col.l}</label>
    <input type="${this._typeToHtml(col.t)}" class="form-control" name="${col.n}" id="te-${col.n}" value="${cv}" placeholder="${col.l}">
</div>
            `;
            }
        }

        // output
        openModalHTML(`
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button"></button>
        <h4 class="modal-title">Редактирование</h4>
    </div>
    <div class="modal-body">
        <form data-teid="${id}" class="form" onsubmit="return false;">
            ${input}
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Закрыть</button>
        <button class="btn btn-primary" data-dismiss="modal" onclick="TableEditor.onRowSave('${id}', ${r});return false">Сохранить</button>
    </div>
`);
    },
    onRowSave: function (id, r) {
        const val = this._loadValue(id);
        const form = document.querySelector(`form[data-teid="${id}"]`);
        const data = new FormData(form);
        const row = {};
        for (const col of val.s) {
            const vvv = document.querySelector(`#te-${col.n}`);
            if (vvv.type === 'file') {
                row[col.n] = vvv.dataset.val;
            } else {
                row[col.n] = data.get(col.n);
            }
        }

        let isNew = false;
        if (r != null) {
            // update
            val.v[r] = row;
        } else {
            // add
            r = val.v.length;
            row['__i'] = r;
            val.v.push(row);
            isNew = true;
        }

        this._storeValue(id, val);
        if (isNew) {
            this._addVisualRow(id, r, row);
        } else {
            this._editVisualRow(id, r, row);
        }
    },
    onRowDelete: function (id, r) {
        const val = this._loadValue(id);
        const newV = [];
        let i = 0;
        for (const row of val.v) {
            if (i++ !== r) {
                newV.push(row);
            }
        }
        val.v = newV;
        this._storeValue(id, val);
        document.querySelector(`tr[data-r="${r};${id}"]`).remove();

        // update ids
        this._getElementsElement(id).innerHTML = '<tr class="heading"></tr>';

        for (const col of val.s) {
            this._addVisualColumn(id, col.l);
        }

        this._addVisualColumn(id, 'Действия');

        i = 0;
        for (const row of val.v) {
            this._addVisualRow(id, i++, row);
        }
    },
    onUpdateImage: function (id, n, i) {
        if (!i.files[0]) {
            return;
        }

        document.querySelector(`#te-${n}-img`).innerHTML = '<b>Загружаю...</b>';

        let fd = new FormData();
        fd.append('file', i.files[0]);
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (r) {
                document.querySelector(`#te-${n}-img`).src = r;
                i.dataset.val = r;
            }
        });
    },
    onUpdateFile: function (id, n, i) {
        if (!i.files[0]) {
            return;
        }

        document.querySelector(`#te-${n}-file`).innerHTML = '<b>Загружаю...</b>';

        let fd = new FormData();
        fd.append('file', i.files[0]);
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (r) {
                document.querySelector(`#te-${n}-file`).innerHTML = r;
                i.dataset.val = r;
            }
        });
    },

    // -------- PRIVATE --------
    _typeToHtml: function (type) {
        switch (type) {
            case 'int':
                return 'number';
        }

        return 'text';
    },

    _storeValue: function (id, val) {
        this._getInputElement(id).value = JSON.stringify(val);
    },
    _loadValue: function (id) {
        return JSON.parse(this._getInputElement(id).value);
    },
    _isValAnImage: function (id, n) {
        const v = this._loadValue(id);
        for (const col of v.s) {
            if (col.n === n) {
                return col.t.startsWith('image');
            }
        }

        return false;
    },
    _isValAFile: function (id, n) {
        const v = this._loadValue(id);
        for (const col of v.s) {
            if (col.n === n) {
                return col.t.startsWith('file');
            }
        }

        return false;
    },

    _addVisualColumn: function (id, name) {
        const el = document.createElement('td');
        el.style.overflow = 'hidden'; // todo: figure out this one
        el.style.background = '#eee';
        el.style.color = '#000';
        el.innerText = name;

        this._getHeaderElement(id).appendChild(el);
    },
    _addVisualRow: function (id, r, val) {
        let valdata = '';
        for (const k of Object.keys(val)) {
            if (k === '__i') continue;
            if (!this._isValAnImage(id, k)) {
                valdata += `<td>${val[k]}</td>`;
            } else {
                if (!this._isValAFile(id, k)) {
                    valdata += `<td><img src="${val[k]}" style="max-width: 100px; max-height: 100px;" /></td>`;
                } else {
                    valdata += `<td><p>${val[k]}</p></td>`;
                }
            }
        }

        this._getElementsElement(id).innerHTML += `
        <tr data-r="${r};${id}">
        ${valdata}
        <td>
        <button class="btn" onclick="TableEditor.onRowEdit('${id}', ${r}); return false"><i class="fa fa-pencil"></i></button>
        <button class="btn" onclick="TableEditor.onRowDelete('${id}', ${r}); return false"><i class="fa fa-trash-o"></i></button>
        </td>
</tr>
        `;
    },
    _editVisualRow: function (id, r, val) {
        let valdata = '';
        for (const k of Object.keys(val)) {
            if (k === '__i') continue;
            if (!this._isValAnImage(id, k)) {
                valdata += `<td>${val[k]}</td>`;
            } else {
                if (!this._isValAFile(id, k)) {
                    valdata += `<td><img src="${val[k]}" style="max-width: 100px; max-height: 100px;" /></td>`;
                } else {
                    valdata += `<td><p>${val[k]}</p></td>`;
                }
            }
        }
        valdata += `<td>
        <button class="btn" onclick="TableEditor.onRowEdit('${id}', ${r}); return false"><i class="fa fa-pencil"></i></button>
        <button class="btn" onclick="TableEditor.onRowDelete('${id}', ${r}); return false"><i class="fa fa-trash-o"></i></button>
        </td>`;

        document.querySelector(`tr[data-r="${r};${id}"]`).innerHTML = valdata;
    },

    _getInputElement: function (id) {
        return document.querySelector(`input[data-id="${id}"]`);
    },
    _getContainerElement: function (id) {
        return document.querySelector(`div[data-id="${id}"]`);
    },
    _getTableElement: function (id) {
        return this._getContainerElement(id).querySelector('table');
    },
    _getHeaderElement: function (id) {
        return this._getTableElement(id).querySelector('tbody > .heading');
    },
    _getElementsElement: function (id) {
        return this._getTableElement(id).querySelector('tbody');
    }
};