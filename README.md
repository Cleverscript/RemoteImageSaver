# saveRemoteImg 1C-Bitrix
Function from Bitrix API save remote src image, resize & add watermark, return id image from `b_file` table of false.

#### Array format for watermark:
```php
["name" => "watermark", "position" => "center", "file"=>"FILE_PATH"];
```

#### Resize Types:

- BX_RESIZE_IMAGE_EXACT
- BX_RESIZE_IMAGE_PROPORTIONAL
- BX_RESIZE_IMAGE_PROPORTIONAL_ALT

More details [ResizeImageGet](https://dev.1c-bitrix.ru/api_help/main/reference/cfile/resizeimageget.php)


### Usege
```php
$MainPictureUrl = 'https://cbu01.alicdn.com/img/ibank/O1CN01Ey8nb326WubqZsCiZ_!!2244787670-0-cib.jpg';

if ($id = saveRemoteImg(
    $MainPictureUrl, 
    800, 800, 
    BX_RESIZE_IMAGE_EXACT,
    [
        "name" => "watermark", 
        "position" => "center", 
        "file" => $_SERVER['DOCUMENT_ROOT'] . '/upload/wm.png'
    ])) {

    $file = \CFile::GetFileArray($id);

    echo "<img src=\"$file[SRC] \"/>";
}	
```

