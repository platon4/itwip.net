<form id="formSave">
    <div id="modal_moderation_status">
        <div class="selects">
            <?php echo Html::dropDownList('_status', $account['_status'], array(0 => 'На модерации', 1 => 'Работает', 2 => 'Не допущен')); ?>
        </div>
    
        <div class="description">
            <textarea placeholder="Написать почему отклонен аккаунт"></textarea>
        </div>
    </div>
</form>