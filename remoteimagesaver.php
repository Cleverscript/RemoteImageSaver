<?php 
/**
 * Class from Bitrix API save remote image, resize & add watermark
 * $src - string url path to remote img
 * $width - int 
 * $height - int 
 * $mode - boolean, false: return ID file from table b_file, true: make file array
 * $resizeType - integer
 * $watermark - array ["name" => "watermark", "position" => "center", "file"=>"FILE_PATH"]
 *
 * @version   $Id: remoteimagesaver.php 2024-02-06 23:20:00Z itscript $
 * @package   RemoteImageSaver
 * @author    Dokukin Vyacheslav Olegovich <toorrp4@gmail.com> https://itscript.ru
 * @copyright Copyright (c) 2023-2024 Itscript.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @return    id image from `b_file` table of false
 */
 abstract class RemoteImageSaver
 {
     public static function save(string $src, int $width, int $height, bool $mode = true, $resizeType = null, array $watermark = []): bool|int|array
     {
         $hash = md5($src);
         $savePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/remote_img';
 
         $makeFileArray = \CFile::MakeFileArray($src);
 
         $ext = match(true) {
             str_contains($makeFileArray['type'], 'jpeg') => 'jpg',
             str_contains($makeFileArray['type'], 'gif') => 'gif',
             str_contains($makeFileArray['type'], 'png') => 'png',
             default => 'jpg'
         };
 
         if (!file_exists($savePath)) { 
             mkdir($savePath);
         }
 
         $originFileDst = $savePath . '/origin_' . $hash . '.' . $ext;
         $resizeFileDst = $savePath . '/resize_' . $hash . '.' . $ext;
 
         if (!file_exists($makeFileArray['tmp_name'])) { 
             return false;
         }
         if (!copy($makeFileArray['tmp_name'], $originFileDst)) { 
             return false;
         }
 
         $fileContent = $originFileDst;
 
         if ($width && $height) {
 
             $resizeType ??= BX_RESIZE_IMAGE_PROPORTIONAL;
 
             $resize = \CFile::ResizeImageFile(
                 $originFileDst,
                 $resizeFileDst,
                 array('width' => $width, 'height' => $height),
                 $resizeType,
                 $watermark,
                 false,
                 false,
             );
 
             if(!$resize) { 
                 return false;
             }
 
             $makeFileArray = \CFile::MakeFileArray($resizeFileDst);
             $fileContent = $resizeFileDst;
         }
 
         if (!$mode) {
 
             $id = \CFile::SaveFile(
                 [
                     "name" => $makeFileArray['name'],
                     "size" => $makeFileArray['size'],
                     "tmp_name" => $makeFileArray['tmp_name'],
                     "type" => $makeFileArray['type'],
                     "old_file" => null,
                     "del" => "N",
                     "MODULE_ID" => "iblock",
                     "description" => "",
                     "content" => file_get_contents($fileContent)
                 ],
                 ''
             );
         
             self::remove([$originFileDst, $resizeFileDst]);
         
             return $id;
         } else{
             $makeFileArray['TRASH'][] = $originFileDst;
             $makeFileArray['TRASH'][] = $resizeFileDst;
             return $makeFileArray;
         }
 
         return false;
 
     }
 
     // Remove trash images
     public static function remove(array $arr) {
         if (!empty($arr)) {
             foreach ($arr as $src) {
                 unlink($src);
             }
         }
     }
 
 }

