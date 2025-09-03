<?php
    function check_code($width=120,$height=40,$char_num=4,$fontSize = 20){

        header("Content-type: image/jpeg");
        $img = imagecreatetruecolor($width, $height);

        //随机生成一个颜色
        $red = rand(0, 255);
        $green = rand(0, 255);
        $blue = rand(0, 255);
        $random_color = imagecolorallocate($img, $red, $green, $blue);
        imagefill($img, 0, 0, $random_color);

        $rect_color = imagecolorallocate($img, 0, 0, 0);

        //随机给图片分布一些杂乱的像素
        //设置颜色
        $pixel_color = $rect_color;
        for ($i = 0; $i < 100; $i++) {
            //随机分布像素
            imagesetpixel($img, rand(0, 114), rand(0, 34), $pixel_color);
        }

        //在图片中增加线条
        //颜色
        $line_color = $pixel_color;
        $line_num = rand(1, 2);
        for ($i = 0; $i < $line_num; $i++) {
            imageline($img, rand(0, 50), rand(0, 34), rand(90, 114), rand(0, 34), $line_color);
        }

        //图片中增加文字
        //生成随机的字符,chr() 函数可将数值转换成对应的字符
        $str = "";
        for ($i = 0; $i < $char_num; $i++){
            $str .= chr(rand(65, 90));
        }

        //或使用字体更加丰富的 imagettftext()
        //注: 使用该方法时,注意字体文件的路径,要指定成绝对路径
        imagettftext($img, $fontSize, rand(0, 10), rand(15, 30), rand(30, 31), $line_color, '/PHPProject/BBS/style/ManyGifts.ttf', $str);

        imagejpeg($img);

        //释放图片
        imagedestroy($img);

        //返回验证码字符串
        return $str;
    }
?>