<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/index.css" />
</head>

<body>
    <main class="container" role="main" aria-labelledby="login-title">
        <h1 id="login-title">LOGIN</h1>
        <form id="login-form" autocomplete="off" aria-describedby="login-desc">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Username" required aria-required="true"
                autocomplete="username" />

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Password" required aria-required="true"
                autocomplete="current-password" />

            <input type="submit" name="login" value="Login" />
            <div class="feedback" aria-live="polite"></div>
        </form>
    </main>

    <script src="assets/js/global.js"></script>
    <script>
        document.getElementById('login-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const body = {
                username : document.getElementById('username').value,
                password : document.getElementById('password').value,
                login    : 1
            }

            let data = await callAPI({
                url : './api/auth.php',
                body
            });
            const feedback = document.querySelector('.feedback');
            feedback.textContent = data.message;

            data = data.data;
            if (data.status === '200') {
                feedback.style.color = 'green';
                const role = data.data.role;
                setTimeout(() => {
                    if (role === 'admin') {
                        window.location.href = './admin/dashboard.php';
                    }else {
                        window.location.href = './user/dashboard.php';
                    }
                    // save user role in localStorage
                    localStorage.setItem('user_role', role);
                }, 1500);
            } else {
                feedback.style.color = 'red';
            }
        });
    </script>

</body>

</html>