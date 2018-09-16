<?php
return [
//  'path' => 'controller@action'
    "/login/in" => 'n\modules\index\controllers\LoginController@In',
    "/item/item" => 'n\modules\index\controllers\ItemController@item',
    '/item/items' => 'n\modules\index\controllers\ItemController@items',
    '/item/toggle-visible-range' => 'n\modules\index\controllers\ItemController@ToggleVisibleRange',
    '/item/parent-dir' => 'n\modules\index\controllers\ItemController@ParentDir',
    '/item/save-item' => 'n\modules\index\controllers\ItemController@saveItem',
    '/item/rank' => 'n\modules\index\controllers\ItemController@rank',
    "/item/item-draft" => 'n\modules\index\controllers\ItemController@itemDraft',
    "/item/todo-list" => 'n\modules\index\controllers\ItemController@todoList',
    "/item/todo-done-today" => 'n\modules\index\controllers\ItemController@todoDoneToday',
    "/item/collection-done-today" => 'n\modules\index\controllers\ItemController@collectionDoneToday',
    '/note/item-notes' => 'n\modules\index\controllers\NoteController@itemNotes',
    '/note/move-note' => 'n\modules\index\controllers\NoteController@moveNote',
    "/note/note" => 'n\modules\index\controllers\NoteController@note',
    "/note/note-done" => 'n\modules\index\controllers\NoteController@noteDone'
];