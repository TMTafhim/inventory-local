<?php
$login_logo = !empty($organizationlogo) ? $organizationlogo : "image/Doel e-Services.png";
$login_name = !empty($organization_name) ? $organization_name : "Doel e-Services";
function loginHtml($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<style>
body.login-page {
  display: block !important;
  min-height: 100vh;
  background: #f3f6fa;
  color: #172033;
}
.login-clean {
  min-height: 100vh;
  display: grid;
  grid-template-rows: auto 1fr auto;
  padding: 22px;
}
.login-top {
  width: 100%;
  max-width: 1080px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}
.login-brand {
  display: inline-flex;
  align-items: center;
  gap: 11px;
  color: #172033;
  text-decoration: none;
}
.login-brand:hover {
  color: #172033;
  text-decoration: none;
}
.login-brand img {
  width: 40px;
  height: 40px;
  object-fit: contain;
  padding: 5px;
  border-radius: 8px;
  border: 1px solid #d9e3ee;
  background: #ffffff;
}
.login-brand span {
  max-width: 330px;
  font-size: 15px;
  font-weight: 850;
  line-height: 1.22;
}
.login-pill {
  min-height: 32px;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  border: 1px solid #d9e3ee;
  border-radius: 8px;
  background: #ffffff;
  padding: 7px 10px;
  color: #52616f;
  font-size: 12px;
  font-weight: 800;
}
.login-pill i {
  color: #0f766e;
}
.login-center {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 28px 0;
}
.login-panel {
  width: 100%;
  max-width: 430px;
  border: 1px solid #d9e3ee;
  border-radius: 8px;
  background: #ffffff;
  box-shadow: 0 22px 60px rgba(15, 23, 42, .12);
  overflow: hidden;
}
.login-panel-head {
  padding: 26px 28px 16px;
  border-bottom: 1px solid #edf2f7;
}
.login-logo {
  width: 52px;
  height: 52px;
  object-fit: contain;
  padding: 6px;
  border: 1px solid #dbe5ef;
  border-radius: 8px;
  background: #ffffff;
  margin-bottom: 16px;
}
.login-panel h1 {
  margin: 0 0 6px;
  color: #111827;
  font-size: 24px;
  font-weight: 850;
  letter-spacing: 0;
}
.login-panel p {
  margin: 0;
  color: #64748b;
  font-size: 13px;
}
.login-form {
  padding: 22px 28px 26px;
}
.login-field {
  margin-bottom: 14px;
}
.login-field label {
  display: block;
  margin-bottom: 7px;
  color: #334155;
  font-size: 13px;
  font-weight: 800;
}
.login-input {
  position: relative;
}
.login-input .form-control {
  height: 46px;
  border: 1px solid #cfd9e5;
  border-radius: 8px;
  padding: 10px 42px 10px 13px;
  color: #111827;
  font-size: 14px;
  box-shadow: none;
}
.login-input .form-control:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
}
.login-input i {
  position: absolute;
  top: 50%;
  right: 14px;
  transform: translateY(-50%);
  color: #718096;
  font-size: 14px;
}
.login-options {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 32px;
  margin: 2px 0 16px;
}
.login-options .icheck-primary {
  margin: 0;
}
.login-options label {
  color: #334155;
  font-size: 13px;
  font-weight: 700;
}
.login-submit {
  width: 100%;
  height: 46px;
  border: 0;
  border-radius: 8px;
  background: #2563eb;
  color: #ffffff;
  font-weight: 850;
  box-shadow: 0 10px 18px rgba(37, 99, 235, .2);
}
.login-submit:hover {
  background: #1d4ed8;
}
.login-foot {
  width: 100%;
  max-width: 1080px;
  margin: 0 auto;
  color: #7b8794;
  font-size: 12px;
  text-align: center;
}
@media (max-width: 575.98px) {
  .login-clean {
    min-height: 100svh;
    padding: 14px;
  }
  .login-top {
    justify-content: center;
  }
  .login-brand span {
    max-width: 250px;
    font-size: 13px;
  }
  .login-pill,
  .login-foot,
  .login-panel p {
    display: none;
  }
  .login-center {
    align-items: start;
    padding: 14px 0 0;
  }
  .login-panel-head {
    padding: 20px 20px 12px;
  }
  .login-logo {
    width: 46px;
    height: 46px;
    margin-bottom: 12px;
  }
  .login-panel h1 {
    font-size: 22px;
  }
  .login-form {
    padding: 18px 20px 20px;
  }
  .login-field {
    margin-bottom: 12px;
  }
  .login-input .form-control,
  .login-submit {
    height: 44px;
  }
}
</style>

<main class="login-clean">
  <header class="login-top">
    <a href="<?php echo loginHtml($base_url); ?>" class="login-brand">
      <img src="<?php echo loginHtml($login_logo); ?>" alt="<?php echo loginHtml($login_name); ?>">
      <span><?php echo loginHtml($login_name); ?></span>
    </a>
    <div class="login-pill"><i class="fas fa-lock"></i> Secure Portal</div>
  </header>

  <section class="login-center">
    <div class="login-panel">
      <div class="login-panel-head">
        <img src="<?php echo loginHtml($login_logo); ?>" alt="<?php echo loginHtml($login_name); ?>" class="login-logo">
        <h1>Sign In</h1>
        <p>Continue to inventory workspace.</p>
      </div>
      <form action="<?php echo loginHtml($actual_link); ?>" method="post" class="login-form">
        <div class="login-field">
          <label for="login_email">Email</label>
          <div class="login-input">
            <input type="email" class="form-control" id="login_email" placeholder="name@company.com" name="LoginEmail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please Input your Correct Email Address" autocomplete="email" required>
            <i class="fas fa-envelope"></i>
          </div>
        </div>
        <div class="login-field">
          <label for="login_password">Password</label>
          <div class="login-input">
            <input type="password" class="form-control" id="login_password" placeholder="Enter password" name="LoginPassword" autocomplete="current-password" required>
            <i class="fas fa-lock"></i>
          </div>
        </div>
        <div class="login-options">
          <div class="icheck-primary">
            <input type="checkbox" id="remember">
            <label for="remember">Remember me</label>
          </div>
        </div>
        <button type="submit" class="btn btn-primary login-submit" name="LoginResponse">
          Sign In
        </button>
      </form>
    </div>
  </section>

  <footer class="login-foot">Authorized employee access only.</footer>
</main>
