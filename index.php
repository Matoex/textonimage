<!DOCTYPE html>
<html>

<head>
    <title>textonimage</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/main.css" />
</head>

<body class="landing">
    <div id="page-wrapper">
        <article id="main">
            <section class="wrapper style3">
                <div class="inner">
                    <center>
                        <h2>Textonimage</h2>
                    </center>
                </div>
            </section>

            <section class="wrapper style2">
                <div class="inner">
                    <form action="index.php" method="post" enctype="multipart/form-data">
                        <p>
                            <label for="text">Main Text:</label><br />
                            <input type="text" name="text" required="required" />
                        </p>
<!--
                        <p>
                            <label for="watermark">watermark:</label><br/>
                            <input type="text" name="watermark" required="required"/>
                        </p>
!-->
                        <p>
                            <label for="title">Title:</label><br />
                            <input type="text" name="title" required="required" />
                        </p>
                        <p>
                            <label for="imageurl">imageurl:</label>
                            <p>Here you can insert the URL of an iamge. It should be at least 1000x1000 px depending on the length of th text.<br>If you leave this field blank, the background image in this directory will be used.<br>Note: if the imageurl is not valid, the last proper created image will be sent again.
                                <input type="text" name="imageurl" />
                            </p>
                            <input type="submit" value="create">
                    </form>
                </div>
            </section>

            <?php
            if (isset($_POST['title'])  && isset($_POST['text'])) {
                $title = $_POST['title'];
                $text = $_POST['text'];

                if ($title != '' && $text != "") {
                    $watermark = "@Matoex"; //Insert your watermark here

                    function quote($title, $text, $watermark)
                    {
                        if (isset($_POST['imageurl']) && $_POST['imageurl'] != "") {
                            $imageurl = ($_POST['imageurl']);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $imageurl);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                            $data = curl_exec($ch);
                            curl_close($ch);
                            $image = imagecreatefromstring($data);
                        } else {
                            //select the background image if no imageurl is given
                            //$image = imagecreatefromjpeg("greenblue.jpg");
                            $image = imagecreatefrompng("orangered.png");
                            //$image = imagecreatefrompng("ball.png");
                        }

                        $color_white = imagecolorallocate($image, 255, 255, 255);
                        $color_black = imagecolorallocate($image, 0, 0, 0);

                        $font_size = 50;
                        $angle = 0;

                        //select your ttf font file
                        $font = realpath("keepcalm.ttf");
                        //$font = realpath("bebas.ttf");

                        //Main text wordwrapping and font size detecting start   
                        $cut = 21;
                        $text = wordwrap($text, $cut, "\n", FALSE);

                        $width = imagesx($image);
                        $height = imagesy($image);

                        list($left, $bottom, $right,,, $top) = imageftbbox($font_size, $angle, $font, $text);

                        $text = wordwrap($text, 26, "\n", FALSE);
                        while ($right < 800) {
                            $font_size++;
                            list($left, $bottom, $right,,, $top) = imageftbbox($font_size, $angle, $font, $text);
                        }
                        $font_size = $font_size - 5;
                        $x = 150;
                        $text_height = $top - $bottom;
                        $y = ($height / 2) + $text_height / 2 + 45;

                        imagettftext($image, $font_size, $angle, $x + 9, $y + 9, $color_black, $font, $text);
                        imagettftext($image, $font_size, $angle, $x, $y, $color_white, $font, $text);
                        //Main Text end

                        //Title start
                        $font_size = 46;

                        list($left, $bottom, $right,,, $top) = imageftbbox($font_size, $angle, $font, $title);

                        $x = 100;
                        $y = 100 - $top;

                        imagettftext($image, $font_size, $angle, $x + 5, $y + 5, $color_black, $font, $title);
                        imagettftext($image, $font_size, $angle, $x, $y, $color_white, $font, $title);
                        //Title end

                        //Watermark start
                        $font_size = 35;

                        list($left, $bottom, $right,,, $top) = imageftbbox($font_size, $angle, $font, $watermark);

                        imagettftext($image, $font_size, $angle, $x + 5, $height - 100 - $top + 5, $color_black, $font, $watermark);
                        imagettftext($image, $font_size, $angle, $x, $height - 100 - $top, $color_white, $font, $watermark);
                        //Watermark end


                        header('Content-type: image/jpeg');
                        imagejpeg($image);
                        imagejpeg($image, 'createdimage.jpg');


                        imagedestroy($image);
                        //End of Imagecreation

                        //Start of Telegram image sending
                        $token = "123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11"; //your Telegram bot token
                        $chat_id = "-123456789"; //your Telegram chatid
                        $bot_url = "https://api.telegram.org/bot" . $token . "/";
                        $url = $bot_url . "sendPhoto?chat_id=" . $chat_id;

                        $post_fields = array(
                            'chat_id' => $chat_id,
                            'photo' => new CURLFile(realpath("createdimage.jpg"))
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            "Content-Type:multipart/form-data"
                        ));
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
                        $output = curl_exec($ch);
                    }

                    quote($title, $text, $watermark);
                }
            }
            ?>

    </div>
    </article>
</body>

</html>