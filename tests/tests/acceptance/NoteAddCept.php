<?php
// 感觉这种过程式的测试很不灵活，后期有空看一看跟进一步的一些用法（yii 中就是采用更进一步的用法）
// 写选择器之前，先用 document.querySelect(selectors) 先测试一下能否选择到元素
$I = new WebGuy($scenario);

$I->wantTo('测试添加笔记功能是否正常');
$I->amOnPage("/?fid=2");

$I->amGoingTo("单击左侧第一个事项后,笔记列会有内容显示");
$I->click('#j_items div.panel-body > ul > li:nth-child(1)');
$notesCount = $I->executeJS('return $("#j_notes > li").length');
$newNote = $I->executeJS('return $("#j_notes textarea").length');

if ($newNote) {
    $I->expect("auto create a note and focused");
    $I->seeElement('#j_notes textarea');
    // 保存,并检查内容是为 “ ”
    $I->amGoingTo('按下 ESC 键，检查是否自动');
    $I->pressKey('#c_notes li:nth-child(1) textarea', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
    // 空的 div 依靠 css 去控制其显示
    $I->see('', '#c_notes li:nth-child(1) .textarea');
}else{
    // 新增一条笔记
    $I->wantToTest('测试新增笔记功能');
    $I->amGoingTo("click add note button");
    $I->click('#c_notes li:nth-child(1) .pull-right a[title="添加笔记"]');
    assert('' === $I->grabTextFrom('#c_notes li:nth-child(1) textarea'), '内容为空');
    $I->amGoingTo('添加带有代码块的内容,并且保存');
    $text = <<<'HTML'
``` php
<?php
echo test;
```
HTML;
    $firstNoteTextarea = "#c_notes li:nth-child(1) textarea";
    $I->fillField($firstNoteTextarea, $text);
    $I->pressKey('#c_notes li:nth-child(1) textarea', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
    $I->see('echo test;', '#c_notes li:nth-child(1) .textarea');
    $I->expectTo('该笔记中的代码块被高亮显示了');
    $I->seeElement("#c_notes ul > li:nth-child(1)  pre .hljs");
}