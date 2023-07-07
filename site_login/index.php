<?php
include "config.php";
// Encrypt cookie
function encryptCookie($value)
{
   $key = hex2bin(openssl_random_pseudo_bytes(4));
   $cipher = "aes-256-cbc";
   $ivlen = openssl_cipher_iv_length($cipher);
   $iv = openssl_random_pseudo_bytes($ivlen);
   $ciphertext = openssl_encrypt($value, $cipher, $key, 0, $iv);
   return (base64_encode($ciphertext . '::' . $iv . '::' . $key));
}
// Decrypt cookie
function decryptCookie($ciphertext)
{
   $cipher = "aes-256-cbc";
   list($encrypted_data, $iv, $key) = explode('::', base64_decode($ciphertext));
   return openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);
}
// Check if $_SESSION or $_COOKIE already set
if (isset($_SESSION['userid'])) {
   header('Location: home.php');
   exit;
} else if (isset($_COOKIE['rememberme'])) {
   // Decrypt cookie variable value
   $userid = decryptCookie($_COOKIE['rememberme']);
   // Fetch records
   $stmt = $conn->prepare("SELECT count(*) as cntUser FROM users WHERE id=:id");
   $stmt->bindValue(':id', (int) $userid, PDO::PARAM_INT);
   $stmt->execute();
   $count = $stmt->fetchColumn();
   if ($count > 0) {
      $_SESSION['userid'] = $userid;
      header('Location: home.php');
      exit;
   }
}
// On submit
if (isset($_POST['but_submit'])) {
   $username = $_POST['txt_uname'];
   $password = $_POST['txt_pwd'];
   if ($username != "" && $password != "") {
      // Fetch records
      $stmt = $conn->prepare("SELECT count(*) as cntUser,id FROM users WHERE username=:username and password=:password ");
      $stmt->bindValue(':username', $username, PDO::PARAM_STR);
      $stmt->bindValue(':password', $password, PDO::PARAM_STR);
      $stmt->execute();
      $record = $stmt->fetch();
      $count = $record['cntUser'];
      if ($count > 0) {
         $userid = $record['id'];
         if (isset($_POST['rememberme'])) {
            // Set cookie variables
            $days = 30;
            $value = encryptCookie($userid);
            setcookie("rememberme", $value, time() + ($days * 24 * 60 * 60 * 1000));
         }
         session_start();
         $_SESSION['userid'] = $userid;
         header("Location: home.php");

      } else {
         echo "Usuário ou Senha Invalida!";
      }
   }
}
?>
<!doctype html>
<html>

<head>
   <title>Login page with Remember me using PDO and PHP</title>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Azure Create WorkItens Link</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
</head>
</head>

<body>
   <form method="post" action="">
      <div class="form-row">
         <div class="parent container d-flex justify-content-center align-items-center h-100">
            <div class="child">
               </br>
               </br>
               <h1>Login</h1>
               <label for="vUser">Usuário:</label>
               <input type="text" required="true" class="form-control form-control-lg" name="txt_uname" value=""
                  placeholder="Usuário" /></br>
               <label for="vPassword">Senha:</label>
               <input type="password" required="true" class="form-control form-control-lg" name="txt_pwd" value=""
                  placeholder="Senha" /></br>
               <input type="checkbox" name="rememberme" class="form-check-input" value="1" />&nbsp;Lembrar-Me
               </br>
               </br>
               <input type="submit" value="Submit" class="btn btn-primary btn-lg btn-block" name="but_submit"
                  id="but_submit" />
            </div>
         </div>
      </div>
   </form>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous">
      </script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
      integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
      </script>
</body>

</html>