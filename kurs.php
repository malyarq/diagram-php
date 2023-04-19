<?php
$fileN = "data.dat"; //Название открываемого файла
$h = $w = 500; //Размеры изображения
if (is_file($fileN))
{
  $fd = fopen($fileN, "r");
  $sum = 0;
  while (!feof($fd))
  {
    $a = trim(fgets($fd)); //Отбрасываем пустые строки
    if (!empty($a))
    {
      $exp = explode(" ", $a);
      $sector[$exp[1]] = $exp[0]; //В $exp[1] - название, в $exp[0] - кол-во.
      $sum += $exp[0]; //Считаем сумму кол-ва
      $r[$exp[1]] = $exp[2]; //Красный
      $g[$exp[1]] = $exp[3]; //Зелёный
      $b[$exp[1]] = $exp[4]; //Синий
    }
  }
  fclose($fd);
  if (isset($_GET['img'])) //Когда браузер запрашивает картинку мы рисуем сначала шаблон
  {
    $img = imagecreatetruecolor($w+1,$h+1); //Создание пустой картинки заданных размеров
    if (!$img) exit;
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 1, 1, $white); //Заливка её белым цветом
    $background = imagecolorallocate($img, 240, 240, 240);
    $cx = $cy = $h/2; //Центр по обеим осям
    imagefilledellipse($img ,$cx, $cy, $w ,$h, $background); //Отрисовка круга
  }
  else //Всё, что не касается картинки
  {
    $table = "<html><head><title>Диаграмма</title></head>
<body>
<center>
    <img src=".$_SERVER['PHP_SELF']."?img=diagram>
    <table style='font-size: 24px;' border=1 cellpadding='3' cellspacing='0'>
      <tr align='center'>
        <td> Цвет </td>
        <td> Кол-во </td>
        <td> % </td>
        <td> Название </td>
      </tr>
</center>";
  }
  $start = 0; //Начальное значение угла
  foreach ($sector as $key =>$val) //В $key - название, в $val - кол-во.
  {
    $ugol = $val / $sum * 360; //Угол, который будет занимать сектор
    if ($ugol > 0)
    {
      if  (isset($_GET['img'])) //Когда браузер запрашивает картинку мы рисуем уже отдельные сектора
      {
        $color =  imagecolorallocate($img, $r[$key], $g[$key], $b[$key]); //Цвет сектора
        $angle_sector = $start + $ugol; //Конечное значение угла сектора
        imagefilledarc($img ,$cx, $cy, $w, $h, $start, $angle_sector, $color, IMG_ARC_PIE); //Закрашивание нужного сектора
        $start += $ugol; //Следующее начальное значение угла сектора
      }
      else //Всё, что не касается картинки(таблица)
      {
        $table .= "
  <tr align='center'>
    <td bgcolor='".sprintf("#%02x%02x%02x", $r[$key], $g[$key], $b[$key])."'></td>
    <td> $val </td>
    <td> ".sprintf("%.2f",($val/$sum*100))." % </td>
    <td> $key </td>
  </tr>";
      }
    }
  }
  if (isset($_GET['img'])) //Окончательный вывод картинки
  {
    header ("Content-type: image/jpeg");
    imagepng($img);
  }
  else //Окончательный вывод всего остального
  {
     $table .="
</table>
</body>
</html>";
     echo $table;
  }
}
else {echo "Файл data.dat не найден";exit;} //Ошибка в случае отсутствия файла с данными
?>
