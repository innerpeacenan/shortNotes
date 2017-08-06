<?php
// 感觉这种过程式的测试很不灵活，后期有空看一看跟进一步的一些用法（yii 中就是采用更进一步的用法）
// 写选择器之前，先用 document.querySelect(selectors) 先测试一下能否选择到元素
$I = new WebGuy($scenario);

$I->wantTo('测试主页是否工作正常');
$I->amOnPage("/?fid=2");

$I->amGoingTo("单击左侧第一个事项后,笔记列会有内容显示");
$I->dontSeeElement("#j_notes > li:nth-child(1)");
$I->click('#j_items div.panel-body > ul > li:nth-child(1)');
$notesCount = $I->executeJS('return $("#j_notes > li").length');

$I->wantToTest('测试笔记编辑和保存功能');
$I->amGoingTo('双击笔记，添加带有代码块的内容');
$firstNoteDiv = 'ul#j_notes li:nth-child(1) .textarea';
$I->doubleClick($firstNoteDiv);
$firstNoteTextarea = "#c_notes li:nth-child(1) textarea";
$text = <<<'HTML'
``` php
<?php
echo test;
```
HTML;
$I->fillField($firstNoteTextarea, $text);
$I->amGoingTo('点击保存按钮保存笔记');
$I->click('#c_notes li:nth-child(1) .pull-right a[title="保存笔记"]');
$I->expect('新添加的内容被保存，并且代码块被正确渲染');
$I->see('echo test', $firstNoteDiv);
$I->expectTo('该笔记中的代码块被高亮显示了');
$I->seeElement("#c_notes ul > li:nth-child(1)  pre .hljs");
