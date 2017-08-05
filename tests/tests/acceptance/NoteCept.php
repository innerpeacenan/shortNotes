<?php
$I = new WebGuy($scenario);
$I->wantTo('see test on page');
$I->amOnPage("/?fid=2");
$I->see('事项列表');
$I->amGoingTo('确认页面刚加载进来的时候，笔记列表是空的');
$I->dontSeeElement("#j_notes:nth-child(1) > li");
$I->amGoingTo("单击左侧第一个事项后,笔记列是否有内容显示");
$I->click('#j_items > div > div.panel-body > ul:nth-child(1) > li');
$I->seeElement("#j_notes:nth-child(1) > li");


