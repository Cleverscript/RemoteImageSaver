# RemoteImageSaver [1C-Bitrix]
Class from Bitrix API save remote image, resize & add watermark, return id image from `b_file` table of false.

#### Array format for watermark:
```php
["name" => "watermark", "position" => "center", "file" => "FILE_PATH"];
```

#### Resize Types:

- BX_RESIZE_IMAGE_EXACT
- BX_RESIZE_IMAGE_PROPORTIONAL
- BX_RESIZE_IMAGE_PROPORTIONAL_ALT

More details [ResizeImageGet](https://dev.1c-bitrix.ru/api_help/main/reference/cfile/resizeimageget.php)


### Usege

If you need to get the file ID
```php
include_once('remoteimagesaver.php');
$MainPictureUrl = 'https://cbu01.alicdn.com/img/ibank/O1CN01Ey8nb326WubqZsCiZ_!!2244787670-0-cib.jpg';

if ($id = RemoteImageSaver::save(
    $MainPictureUrl, 
    800, 800, false, 
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

If you need to add a file to an element
```php
use \Bitrix\Main;
use \Bitrix\Main\Loader;
Loader::includeModule('iblock');

include_once('remoteimagesaver.php');
$MainPictureUrl = 'https://cbu01.alicdn.com/img/ibank/O1CN01Ey8nb326WubqZsCiZ_!!2244787670-0-cib.jpg';

$makeFileArray = RemoteImageSaver::save(
    $MainPictureUrl, 
    400, 400, true, 
    BX_RESIZE_IMAGE_EXACT,
    [
        "name" => "watermark", 
        "position" => "center", 
        "file" => $_SERVER['DOCUMENT_ROOT'] . '/upload/wm.png'
    ]);

if (!empty($makeFileArray)) {

    $el = new \CIBlockElement;
    $arFields = Array(
        "CODE" => 'test',
        "IBLOCK_ID"      => 2,
        "IBLOCK_SECTION_ID"    => 247,
        "PROPERTY_VALUES" => [],
        "NAME"           => 'Test',
        "ACTIVE"         => "Y",
        "PREVIEW_TEXT"   => "",
        "DETAIL_TEXT"    => '',
        "DETAIL_TEXT_TYPE" => 'html',
        "DETAIL_PICTURE" => $makeFileArray
    );
    if ($id = $el->Add($arFields)) {
        echo "Success add element #$id";
    } else {
        echo $el->LAST_ERROR;
    }

    self::remove($makeFileArray['TRASH']);
}
```


