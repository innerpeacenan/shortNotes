
//items.length比较有意思，初步判断是根据mime类型来的，即有几种mime类型，长度就是几（待验证）
//如果粘贴纯文本，那么len=1，如果粘贴网页图片，len=2, items[0].type = 'text/plain', items[1].type = 'image/*'
//如果使用截图工具粘贴图片，len=1, items[0].type = 'image/png'
//如果粘贴纯文本+HTML，len=2, items[0].type = 'text/plain', items[1].type = 'text/html'
// console.log('len:' + len);
// console.log(items[0]);
// console.log(items[1]);
// console.log( 'items[0] kind:', items[0].kind );
// console.log( 'items[0] MIME type:', items[0].type );
// console.log( 'items[1] kind:', items[1].kind );
// console.log( 'items[1] MIME type:', items[1].type );
document.addEventListener('paste', function (event) {

    var isChrome = false;
    // Edge 支持 event.clipboardData属性
    if ( event.clipboardData || event.originalEvent ) {
        //not for ie11  某些chrome版本使用的是event.originalEvent
        clipboardData = (event.clipboardData || event.originalEvent.clipboardData);
        if ( clipboardData.items ) {
            // for chrome
            var  items = clipboardData.items,
                len = items.length,
                blob = null;
            isChrome = true;

            //阻止默认行为即不让剪贴板内容在div中显示出来
            // event.preventDefault();

            //在items里找粘贴的image,据上面分析,需要循环
            for (var i = 0; i < len; i++) {
                if (items[i].type.indexOf("image") !== -1) {
                    //getAsFile() 此方法只是living standard firefox ie11 并不支持
                    blob = items[i].getAsFile();
                }
            }
            if ( blob) {
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onload = function (event) {
                    // event.target.result 即为图片的Base64编码字符串
                    var base64_str = event.target.result
                    VGLOBAL.picture_index +=  1
                    note.modifiedContent += '['+ VGLOBAL.picture_index + ']:' + base64_str;

                }

            }
        }
    }
})


// function uploadImgFromPaste (file, type, isChrome) {
//     var formData = new FormData();
//     formData.append('image', file);
//     formData.append('submission-type', type);
//     var xhr = new XMLHttpRequest();
//     xhr.open('POST', '/upload_image_by_paste');
//     xhr.onload = function () {
//         if ( xhr.readyState === 4 ) {
//             if ( xhr.status === 200 ) {
//                 var data = JSON.parse( xhr.responseText ),
//                     tarBox = document.getElementById('tar_box');
//                 if ( isChrome ) {
//                     var img = document.createElement('img');
//                     img.className = 'my_img';
//                     img.src = data.store_path;
//                     tarBox.appendChild(img);
//                 } else {
//                     var imgList = document.querySelectorAll('#tar_box img'),
//                         len = imgList.length,
//                         i;
//                     for ( i = 0; i < len; i ++) {
//                         if ( imgList[i].className !== 'my_img' ) {
//                             imgList[i].className = 'my_img';
//                             imgList[i].src = data.store_path;
//                         }
//                     }
//                 }
//             } else {
//                 console.log( xhr.statusText );
//             }
//         };
//     };
//     xhr.onerror = function (e) {
//         console.log( xhr.statusText );
//     }
//     xhr.send(formData);
// }