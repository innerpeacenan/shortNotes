<?php
// 感觉这种过程式的测试很不灵活，后期有空看一看跟进一步的一些用法（yii 中就是采用更进一步的用法）
// 写选择器之前，先用 document.querySelect(selectors) 先测试一下能否选择到元素
$I = new WebGuy($scenario);
$I->wantToTest('删除功能');
$I->amOnPage("/?fid=2");
$I->click('#j_items div.panel-body > ul > li:nth-child(1)');

$noteId = $I->grabAttributeFrom('#c_notes li:nth-child(1)', 'id');
$I->amGoingTo('click button:delete');
$I->doubleClick('#c_notes li:nth-child(1) .pull-right a[title="删除笔记"]');

// 之前对删除的判断不准确，内容一样的笔记可能有多个
if ($I->executeJS('return $("#c_notes .textarea").length')) {
    $I->dontSeeElement('#note_' . $noteId);
} else {
    $I->comment('仅有的一个笔记被删除了');
}
