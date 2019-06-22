<?php
return [
//  'path' => 'controller::action'
    "/logout" => 'app\modules\index\controllers\LoginController::Out',
    "/login/in" => 'app\modules\index\controllers\LoginController::In',
//    "/item/item" => 'app\modules\index\controllers\ItemController::item',
//    '/item/items' => 'app\modules\index\controllers\ItemController::items',
    '/item/toggle-visible-range' => 'app\modules\index\controllers\ItemController::ToggleVisibleRange',
    '/item/parent-dir' => 'app\modules\index\controllers\ItemController::ParentDir',
    '/item/save-item' => 'app\modules\index\controllers\ItemController::saveItem',
//    '/item/rank' => 'app\modules\index\controllers\ItemController::rank',
//    "/item/item-draft" => 'app\modules\index\controllers\ItemController::itemDraft',
    "/item/todo-list" => 'app\modules\index\controllers\ItemController::todoList',
    "/item/todo-done-today" => 'app\modules\index\controllers\ItemController::todoDoneToday',
    "/item/collection-done-today" => 'app\modules\index\controllers\ItemController::collectionDoneToday',
//    '/note/item-notes' => 'app\modules\index\controllers\NoteController::itemNotes',
//    '/note/item-backupNotes' => 'app\modules\index\controllers\NoteController::itemBackupNotes',
//    '/note/move-note' => 'app\modules\index\controllers\NoteController::moveNote',
//    "/note/note" => 'app\modules\index\controllers\NoteController::note',
//    "/note/note-done" => 'app\modules\index\controllers\NoteController::noteDone',
    '/note/note-todo' => 'app\modules\index\controllers\NoteController::noteTodo',
    "/image/save" => 'app\modules\index\controllers\ImageController::imageSave',
];