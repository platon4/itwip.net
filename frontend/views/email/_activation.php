<html>
<head>
<title></title>
</head>
<body>
<div style="width: 700px; margin: 0 auto;">
<table style="width: 100%;font-family: Verdana;font-size: 13px;">
  <tr>
    <td style="padding: 10px 0px;">
        <a href=""><img src="http://itwip.net/i/index/logo.png" alt="" /></a>
    </td>
  </tr>
  <tr style="background:#F6F6F6;font-size: 17px;color: #0086B0;">
    <td style="padding: 7px 10px;">
        Вы успешно зарегистрировались на сервисе - Рекламы в социальных сетях
    </td>
  </tr>
  <tr>
    <td style="background:#FCFCFC;color: #959595; font-size: 14px;padding-left: 10px;">
       Ваши регистрационные данные, для авторизации на сервисе
    </td>
  </tr>
  <tr>
    <td style="background:#FCFCFC;padding: 20px 0px 20px 20px;">
       <table style="color:#959595;font-size: 12px;">
         <tr>
           <td><b>Эл.адрес:</b></td>
           <td><?php echo $mail; ?></td>
         </tr>
         <tr>
           <td><b>Пароль:</b></td>
           <td><?php echo $password; ?></td>
         </tr>
       </table>
    </td>
  </tr>
  <tr style="background:#F6F6F6;font-size: 17px;color: #0086B0;">
    <td style="padding: 7px 10px;">
        Последний шаг регистрации - Активация аккаунта
    </td>
  </tr>
  <tr>
    <td style="background:#FCFCFC;color: #959595; font-size: 14px;padding-left: 10px;">
       Для активации аккаунта, и подтверждения Эл.адреса, перейдите по ссылке
    </td>
  </tr>
  <tr>
    <td style="background:#FCFCFC;color:#505050;font-size: 12px; padding: 20px 0px 80px 20px;"><a href="<?php echo $link; ?>" style="color:#0086B0;">Ссылка для активации аккаунта</a></td>
  </tr>
  <tr>
    <td>
        <table style="width: 100%;background:#0091BB;color: #fff;">
          <tr>
            <td style="font-size: 12px; padding: 10px 0px 10px 10px;">
                Это письмо создано автоматически, на него отвечать не нужно. Искренне Ваш, <a href="http://itwip.net" style="color:#fff;">iTwip.net</a>
            </td>
          </tr>
          <tr>
            <td style="padding: 0px 10px 5px;">
            <table style="width: 100%;font-size: 10px;color: #fff;">
              <tr>
                <td>e-mail: support@itwip.net</td>
                <td style="text-align: right">Реклама в социальных сетях, монетизация © 2013</td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
    </td>
  </tr>
</table>
</div>
</body>
</html>