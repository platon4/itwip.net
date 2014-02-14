<table style="width: 100%;font-family: Verdana;font-size: 13px;">
  <tr>
    <td style="padding: 10px 0px;">
        <a href=""><img src="http://itwip.net/i/index/logo.png" alt="" /></a>
    </td>
  </tr>
  <tr>
    <td style="background:#FCFCFC;color: #959595; font-size: 14px;padding-left: 10px;">
      Запрос в службу поддержки
    </td>
  </tr>
  <tr>
    <td style="background:#FCFCFC;color:#505050;font-size: 12px; padding: 10px 20px;">
		<?php echo Html::encode($text); ?>
	</td>
  </tr>
   <tr>
    <td style="background:#FCFCFC;padding: 0 20px;">
       <table style="color:#959595;font-size: 12px; padding-bottom: 10px;">
		  <tr>
		  <td style="background:#FCFCFC;color: #959595; font-size: 14px; padding-bottom: 7px;">
			   Детали запроса
		  </td>
		  </tr>	   
         <tr>
           <td style="padding-left: 10px;"><b>Тип запроса:</b></td>
           <td><?php echo $_type; ?></td>
         </tr>         <tr>
           <td style="padding-left: 10px;"><b>Эл.адрес:</b></td>
           <td><?php echo $mail; ?></td>
         </tr>
         <tr>
           <td style="padding-left: 10px;"><b>IP адрес:</b></td>
           <td><?php echo $ip; ?></td>
         </tr>
         <tr>
           <td style="padding-left: 10px;"><b>Браузер:</b></td>
           <td><?php echo $browse; ?></td>
         </tr>
       </table>
    </td>
  </tr> 
  <tr>
    <td>
        <table style="width: 100%;background:#0091BB;color: #fff;">
          <tr>
            <td style="padding: 0px 10px 5px;">
            <table style="width: 100%;font-size: 10px;color: #fff;">
              <tr>
                <td>Служба поддержки клиента</td>
                <td style="text-align: right">iTwip © <?php echo date('Y'); ?></td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
    </td>
  </tr>
</table>