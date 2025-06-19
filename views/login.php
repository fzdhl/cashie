<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
    href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="views/styles/auth.css">
  </head>

  <body>
    <main>
      <div class="login bg-success">
        <div class="login-leftside">
          <img class="login-icon" src="resources/logopemweb_satu.png" alt="logo" />
          <h1>Hello!</h1>
          <p>Welcome back to our site.</p>
        </div>

        <div class="login-rightside">
          <form class="login-rightside__form" action="?c=UserController&m=loginProcess" method="post">
            <input
              class="login-rightside__username"
              type="text"
              name="username"
              placeholder="Username"
              required
            /> 
            <input
              class="login-rightside__password"
              type="password"
              name="password"
              placeholder="Password"
              required
            />
            <?= $error ?>
            <a class="login-rightside__recover" href="https://github.com/Vlad401-del">Recover password?</a>
            <input class="login-rightside__submit" type="submit" value="Sign In"/>
          </form>


          <div class="login-rightside__account">
            <p class="login-rightside__account-text">
              ——————— Or continue with ———————
            </p>

            <div class="login-rightside__account-logo">
              <div class="logo-container">
                <a href="google ue">
                  <img
                    class="account-logo_google"
                    src="resources/google_icon.png"
                    alt="google"
                  />
                </a>
              </div>
              <div class="logo-container">
                <a href="https://youtu.be/hWOB5QYcmh0?si=aFF4cR_QIQSRp4yn">
                  <img
                    class="account-logo_gmail"
                    src="resources/gmail_icon.png"
                    alt="gmail"
                  />
                </a>
              </div>
              <div class="logo-container">
                <a href="https://id.wikipedia.org/wiki/Fufufafa">
                  <img
                    class="account-logo_apple"
                    src="resources/apple_icon.png"
                    alt="apple"
                  />
                </a>
              </div>
            </div>
          </div>

          <div class="login-rightside__description">
            <p>Don't have an account?</p>
            <a href="?c=UserController&m=registerView">Create an Account!</a>
          </div>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
