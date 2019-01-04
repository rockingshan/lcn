<?php
if (isset($_POST['acc_num']) && $_POST['acc_num'] != NULL) {
  $acc = $_POST['acc_num'];

  if ($_POST['method'] == "TD") {
    $meth = " ".$_POST['method'];
  }elseif ($_POST['method'] == "PD") {
    $meth = " ".$_POST['method'];
  }elseif ($_POST['method'] == "RECON") {
    $meth = "";
  }
$key_word = "ACTV ".$acc.$meth;
$url_key_word = urlencode($key_word);
$full_url = "http://223.29.207.131/duosubscribe5/V5Services/TextMessageDecomposer/CaptureTextMessage.ashx?sms=".$url_key_word;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $full_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);

}

 ?>

<html>
<head>
<title>TD/PD Reconnection</title>
</head>

<body>

  <h3 align="center"> Use following form for TD/PD & reconnection</h3>
  <form action="tdpd.php" method="post">
  <table align="center">
    <tr><td><select name="method">
      <option value="TD">TD</option>
      <option value="PD">PD</option>
      <option value="RECON">Reconnection</option>
    </select>
    <td>Account : </td>
    <td><input type="text" name="acc_num" value="" />
    </tr>
    <tr><td align="center" colspan="3"><input type="submit" value="Submit"></tr>
    </table>
  </form>

</body>

</html>
