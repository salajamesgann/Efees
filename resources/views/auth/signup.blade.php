<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Sign Up</title>
    <link
      rel="preconnect"
      href="https://fonts.gstatic.com/"
      crossorigin=""
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  </head>
  <body
    style="background-color: black; font-family: 'Inter', 'Noto Sans', sans-serif; min-height: 100vh; margin: 0; display: flex; flex-direction: column; overflow-x: hidden;"
  >
    <?php
      $headerStyle = "display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e7f4e7; padding: 0.75rem 2.5rem;";
      $logoTextStyle = "color: #f97316; font-weight: 700; font-size: 1.125rem; line-height: 1.25rem; letter-spacing: -0.015em; margin-left: 0.75rem;";
      $buttonBaseStyle = "min-width: 84px; max-width: 480px; height: 2.5rem; padding: 0 1rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.875rem; letter-spacing: 0.015em; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none;";
      $signUpBtnStyle = $buttonBaseStyle . " background-color: #f97316; color: black;";
      $logInBtnStyle = $buttonBaseStyle . " background-color: #fdba74; color: black;";
      $formContainerStyle = "width: 100%; max-width: 40rem; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;";
      $headingStyle = "color: #f97316; font-weight: 700; font-size: 1.75rem; line-height: 2rem; letter-spacing: -0.015em; text-align: center; padding: 1.25rem 1rem 0.75rem;";
      $labelTextStyle = "color: #fb923c; font-weight: 500; font-size: 1rem; line-height: 1.25rem; padding-bottom: 0.5rem; display: block;";
      $inputStyle = "width: 100%; border-radius: 0.5rem; border: 1px solid #ea9e4a; background-color: #1a1a1a; color: #fb923c; font-size: 1rem; padding: 1rem; outline: none; transition: border-color 0.3s, box-shadow 0.3s;";
      $inputFocusStyle = "border-color: #f97316; box-shadow: 0 0 0 2px #f97316;";
      $loginButtonStyle = "width: 100%; height: 2.5rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.875rem; letter-spacing: 0.015em; background: linear-gradient(to right, #f97316, #facc15); color: black; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(249, 115, 22, 0.5); transition: background 0.3s;";
      $loginButtonHoverStyle = "background: linear-gradient(to right, #facc15, #f97316);";
    ?>
    <div style="flex-grow: 1; display: flex; flex-direction: column;">
      <header style="<?php echo $headerStyle; ?>">
        <div style="display: flex; align-items: center; color: #f97316;">
          <div style="width: 2rem; height: 2rem; flex-shrink: 0;">
            <svg
              viewBox="0 0 48 48"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
              style="width: 100%; height: 100%; color: currentColor;"
            >
              <path
                d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                fill="currentColor"
              ></path>
            </svg>
          </div>
          <h2 style="<?php echo $logoTextStyle; ?>">Efees</h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
          <a href="{{ route('login') }}" style="<?php echo $logInBtnStyle; ?>">Log in</a>
          <a href="{{ route('signup') }}" style="<?php echo $signUpBtnStyle; ?>">Sign up</a>
        </div>
      </header>

      <main style="flex: 1; display: flex; justify-content: center; padding: 1.25rem 1rem;">
        <section style="<?php echo $formContainerStyle; ?>">
          <h2 id="signup" style="<?php echo $headingStyle; ?>">Create your account</h2>

          <form style="display: flex; flex-direction: column; gap: 1rem;" novalidate>
            
            <!-- Full Name split -->
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
              <label style="flex: 1; min-width: 120px;">
                <span style="<?php echo $labelTextStyle; ?>">First Name</span>
                <input type="text" placeholder="First Name" style="<?php echo $inputStyle; ?>"
                  onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                  onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
              </label>

              <label style="width: 80px; flex-shrink: 0;">
                <span style="<?php echo $labelTextStyle; ?>">MI</span>
                <input type="text" maxlength="1" placeholder="M" style="<?php echo $inputStyle; ?>"
                  onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                  onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
              </label>

              <label style="flex: 1; min-width: 120px;">
                <span style="<?php echo $labelTextStyle; ?>">Last Name</span>
                <input type="text" placeholder="Last Name" style="<?php echo $inputStyle; ?>"
                  onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                  onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
              </label>
            </div>

            <label>
              <span style="<?php echo $labelTextStyle; ?>">Student Email</span>
              <input type="email" placeholder="Email" style="<?php echo $inputStyle; ?>"
                onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
            </label>

            <label>
              <span style="<?php echo $labelTextStyle; ?>">Password</span>
              <input type="password" placeholder="Password" style="<?php echo $inputStyle; ?>"
                onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
            </label>

            <label>
              <span style="<?php echo $labelTextStyle; ?>">Confirm Password</span>
              <input type="password" placeholder="Confirm Password" style="<?php echo $inputStyle; ?>"
                onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
            </label>

            <label>
              <span style="<?php echo $labelTextStyle; ?>">Program / Course</span>
              <input type="text" placeholder="e.g., BSIT" style="<?php echo $inputStyle; ?>"
                onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
            </label>

            <label>
              <span style="<?php echo $labelTextStyle; ?>">Current Year Level</span>
              <input type="text" placeholder="e.g., 3rd Year" style="<?php echo $inputStyle; ?>"
                onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
            </label>

            <label>
              <span style="<?php echo $labelTextStyle; ?>">Current Term / Semester</span>
              <input type="text" placeholder="e.g., 1st Semester" style="<?php echo $inputStyle; ?>"
                onfocus="this.style.cssText='<?php echo $inputStyle.$inputFocusStyle; ?>'"
                onblur="this.style.cssText='<?php echo $inputStyle; ?>'"/>
            </label>

            <div style="text-align: center; margin-top: 0.5rem; color:#fb923c; font-size: 0.875rem;">
              Note: Year level and section may change each term.
            </div>

            <button type="submit"
              style="<?php echo $loginButtonStyle; ?>"
              onmouseover="this.style.cssText='<?php echo $loginButtonStyle.$loginButtonHoverStyle; ?>'"
              onmouseout="this.style.cssText='<?php echo $loginButtonStyle; ?>'">
              Sign Up
            </button>
          </form>
        </section>
      </main>
    </div>
  </body>
</html>
