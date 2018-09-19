<?php $this->pageTitle = 'Собираем закупку'?>
<?=$this->html->formOpen();?>
за: <?php
echo $this->html->input(array(
    'name' => 'date',
    'value' => $date
));
$this->widget(array(
    'class' => 'Datepicker',
    'fieldNames' => array('date'),
));
echo $this->html->submit('Собрать');
echo $this->html->formClose();

if (!empty($items)) {?>
    <hr />
    <script type="text/javascript" src="/assets/js/prices.js"></script>
    <script><!--
        dom.ready(function() {
            bindInpsChange('quantity');
            bindInpsChange('price');
            bindInpsChange('sum');
        });
    //--></script>
    <?=$this->html->formOpen(array('method' => 'POST'))?>
    <p>Номер фактуры закупки: <?=$this->html->inputEx($model, 'number')?>&nbsp;<?=$this->html->error($model, 'number')?></p>
    <table class="purchase">
        <tr>
            <th>Товар</th>
            <th>Кол-во</th>
            <th>Цена</th>
            <th>Сумма</th>
        </tr>
        <?php foreach ($items as $i=> $item) {?>
            <tr class="row">
                <?=$this->html->hiddenEx($rowModel, array(
                    'name' => 'article_subcategory_id',
                    'value' => $item['article_subcat_id'],
                    'multiple' =>true,
                ))?>
                <td><?=$item['article_subcat']?></td>
                <td class="quantity"><?=$this->html->hiddenEx($rowModel, array(
                    'name' => 'quantity',
                    'value' => $item['quantity'],
                    'multiple' =>true,
                )), $item['quantity']?></td>
                <td class="price"><?=$this->html->inputEx($rowModel, array(
                    'name' => 'price',
                    'multiple' =>true,
                ))?></td>
                <td class="sum"><?=$this->html->input(array(
                    'name' => 'sum',
                    'readonly' => 'readonly',
                ))?></td>
            </tr>
        <?php }?>
        <tr class="total">
            <td colspan="3"><b>Итого:</b></td>
            <td class="total">0</td>
        </tr>
    </table>
    <?php
    echo $this->html->submit('Сохранить');
    echo $this->html->formClose();
} else {?>
    <p>список пуст.</p>
<?php }