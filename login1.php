<?php
session_start();
include "koneksi.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if($query->num_rows > 0){
        $user = $query->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama']; // ** TAMBAHKAN INI **
            $_SESSION['role'] = $user['role'];
            $_SESSION['nik'] = $user['nik'];
            
            // Update last activity dan online status
            $user_id = $user['id'];
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            
            mysqli_query($conn, "UPDATE users SET 
                last_activity = NOW(),
                is_online = 1,
                last_ip = '$ip_address',
                last_user_agent = '$user_agent'
                WHERE id = '$user_id'");
            
            header("Location: home.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Login - Teras RHK Terintegrasi</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(145deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            position: relative;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .animated-bg span {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            bottom: -150px;
            border-radius: 50%;
            animation: float 25s infinite linear;
        }

        .animated-bg span:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-delay: 0s; animation-duration: 20s; }
        .animated-bg span:nth-child(2) { left: 20%; width: 40px; height: 40px; animation-delay: 2s; animation-duration: 18s; }
        .animated-bg span:nth-child(3) { left: 30%; width: 100px; height: 100px; animation-delay: 4s; animation-duration: 22s; }
        .animated-bg span:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 1s; animation-duration: 25s; }
        .animated-bg span:nth-child(5) { left: 50%; width: 120px; height: 120px; animation-delay: 3s; animation-duration: 28s; }
        .animated-bg span:nth-child(6) { left: 60%; width: 30px; height: 30px; animation-delay: 6s; animation-duration: 15s; }
        .animated-bg span:nth-child(7) { left: 70%; width: 90px; height: 90px; animation-delay: 2.5s; animation-duration: 23s; }
        .animated-bg span:nth-child(8) { left: 80%; width: 50px; height: 50px; animation-delay: 5s; animation-duration: 19s; }
        .animated-bg span:nth-child(9) { left: 90%; width: 70px; height: 70px; animation-delay: 3.5s; animation-duration: 21s; }
        .animated-bg span:nth-child(10) { left: 95%; width: 110px; height: 110px; animation-delay: 4.5s; animation-duration: 26s; }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1200px) rotate(720deg); opacity: 0; }
        }

        /* Main Container */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Left Side - Branding */
        .brand-section {
            background: linear-gradient(145deg, #4158D0, #C850C0);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .brand-section::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .brand-icon {
            font-size: 70px;
            margin-bottom: 25px;
            position: relative;
            z-index: 2;
        }

        .brand-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
            line-height: 1.2;
        }

        .brand-desc {
            font-size: 15px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 35px;
            position: relative;
            z-index: 2;
            max-width: 90%;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            position: relative;
            z-index: 2;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.3s;
        }

        .feature-item:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.25);
        }

        .feature-item i {
            font-size: 22px;
        }

        .feature-item span {
            font-size: 13px;
            font-weight: 500;
        }

        /* Right Side - Form */
        .form-section {
            padding: 50px 45px;
            background: white;
            display: flex;
            flex-direction: column;
        }

        .form-header {
            text-align: left;
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            font-size: 28px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        /* Form Elements - DIPERBAIKI */
        .form-group {
            margin-bottom: 25px;
            width: 100%;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }

        .form-label i {
            color: #4158D0;
            font-size: 16px;
            width: 18px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #95aac9;
            font-size: 16px;
            transition: color 0.3s;
        }

        .input-field {
            width: 100%;
            padding: 14px 45px 14px 45px;
            border: 2px solid #eef2f6;
            border-radius: 14px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8fafc;
            color: #333;
        }

        .input-field:focus {
            border-color: #4158D0;
            outline: none;
            box-shadow: 0 0 0 4px rgba(65, 88, 208, 0.1);
            background: white;
        }

        .input-field:focus + .input-icon {
            color: #4158D0;
        }

        .input-field::placeholder {
            color: #a0b3cc;
            font-size: 14px;
            font-weight: 400;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #95aac9;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #4158D0;
        }

        .password-toggle i {
            font-size: 18px;
        }

        /* Options Row */
        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0 25px 0;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #555;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4158D0;
            margin: 0;
        }

        .forgot-link {
            color: #4158D0;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #C850C0;
            text-decoration: underline;
        }

        /* Login Button */
        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(145deg, #4158D0, #C850C0);
            border: none;
            border-radius: 14px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(65, 88, 208, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button i {
            font-size: 18px;
        }

        /* Alert */
        .alert-modern {
            background: #fee;
            color: #c53030;
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: none;
            animation: slideDown 0.3s ease;
            font-size: 14px;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-modern i {
            font-size: 18px;
        }

        /* Footer */
        .form-footer {
            margin-top: auto;
            text-align: center;
            padding-top: 25px;
            border-top: 2px solid #eef2f6;
            color: #95aac9;
            font-size: 13px;
        }

        /* Responsive Breakpoints */
        @media screen and (max-width: 1024px) {
            .brand-section { padding: 40px 30px; }
            .brand-title { font-size: 28px; }
            .brand-desc { font-size: 14px; max-width: 100%; }
            .form-section { padding: 40px 35px; }
        }

        @media screen and (max-width: 768px) {
            .login-card {
                grid-template-columns: 1fr;
                border-radius: 24px;
            }
            
            .brand-section {
                padding: 35px 30px;
                text-align: center;
            }
            
            .brand-desc {
                margin-left: auto;
                margin-right: auto;
            }
            
            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .form-section {
                padding: 35px 30px;
            }
            
            .form-header h2 {
                font-size: 24px;
            }
            
            .input-field {
                padding: 13px 42px 13px 42px;
            }
        }

        @media screen and (max-width: 480px) {
            body { padding: 10px; }
            
            .brand-section { padding: 30px 20px; }
            .brand-icon { font-size: 50px; }
            .brand-title { font-size: 24px; }
            
            .feature-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .feature-item { padding: 12px; }
            
            .form-section { padding: 30px 20px; }
            .form-header h2 { font-size: 22px; }
            
            .form-group { margin-bottom: 20px; }
            
            .input-field {
                padding: 12px 40px 12px 40px;
                font-size: 14px;
            }
            
            .options-row {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .login-button {
                padding: 12px;
                font-size: 15px;
            }
        }

        @media screen and (max-width: 360px) {
            .brand-section { padding: 25px 15px; }
            .form-section { padding: 25px 15px; }
            .feature-item span { font-size: 12px; }
        }

        /* Landscape Mode */
        @media screen and (max-height: 600px) and (orientation: landscape) {
            .login-wrapper { padding: 10px 0; }
            .brand-section { padding: 25px 30px; }
            .form-section { padding: 25px 30px; }
            .brand-icon { font-size: 40px; margin-bottom: 15px; }
            .brand-title { font-size: 24px; margin-bottom: 10px; }
            .brand-desc { margin-bottom: 20px; }
        }
    </style>
</head>
<body>

    <!-- Animated Background -->
    <div class="animated-bg">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- Left Side - Branding -->
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h1 class="brand-title">Teras RHK</h1>
                <p class="brand-desc">
                    Platform terintegrasi pengelolaan Laporan Rencana Hasil Kerja (RHK)
                    untuk SDM Program Keluarga Harapan (PKH) yang efisien.
                </p>
                <div class="feature-grid">
                    <div class="feature-item">
                        <i class="fas fa-file-alt"></i>
                        <span>Manajemen Laporan</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-file-pdf"></i>
                        <span>Generate PDF</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-copy"></i>
                        <span>Multi Template</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-filter"></i>
                        <span>Filter & Export</span>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="form-section">
                <div class="form-header">
                    <h2>Selamat Datang Kembali!</h2>
                    <p>Silakan login ke akun Anda</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert-modern">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" id="loginForm">
                    <!-- Username Field - DIPERBAIKI -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            <span>Username</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" 
                                   name="username" 
                                   class="input-field"
                                   placeholder="Masukkan username" 
                                   required
                                   autocomplete="username">
                        </div>
                    </div>

                    <!-- Password Field - DIPERBAIKI -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            <span>Password</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="input-field"
                                   placeholder="Masukkan password" 
                                   required
                                   autocomplete="current-password">
                            <button type="button" 
                                    class="password-toggle" 
                                    onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Options Row - DIPERBAIKI -->
                    <div class="options-row">
                        <label class="remember-checkbox">
                            <input type="checkbox" name="remember"> 
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="forgot-link">
                            Lupa password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" name="login" class="login-button">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login ke Dashboard</span>
                    </button>
                </form>

                <!-- Footer -->
                <div class="form-footer">
                    <p>Selamat Mengerjakan Rencana Hasil Kerja</p>
                    <p class="mt-2">
                        <small>&copy; 2026 Sistem RHK Terintegrasi</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (username === '' || password === '') {
                e.preventDefault();
                alert('Username dan password harus diisi!');
            }
        });
    </script>
</body>
</html>