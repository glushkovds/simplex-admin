<input data-id="<?= $jsId ?>" type="hidden" name="<?= $this->inputName() ?>" value="<?= htmlspecialchars($value) ?>" />
<div data-id="<?= $jsId ?>" data-field="<?= $this->name ?>">
    <div>
        <div>
            <button class="btn btn-default btn-sm" onclick="TableEditor.onRowEdit('<?= $jsId ?>', null);return false;">
                <i class="fa fa-plus"></i> Добавить строку
            </button>
        </div>
    </div>
    <div style="overflow: auto">
        <table class="table"><tbody><tr class="heading"></tr></tbody></table>
    </div>
</div>
<script>TableEditor.init('<?= $jsId ?>');</script>