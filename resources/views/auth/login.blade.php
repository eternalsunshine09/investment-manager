<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Porto Tracking</title>
    <style>
    /* Reset dan body */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        background-color: #ffffff;
        font-family: 'Segoe UI', sans-serif;
        overflow: hidden;
        position: relative;
    }

    /* Container utama */
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 1200px;
        gap: 60px;
        padding: 40px;
    }

    /* Form styling */
    .form {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 2.5em;
        background-color: #171717;
        border-radius: 25px;
        transition: .4s ease-in-out;
        width: 380px;
        position: relative;
        z-index: 10;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .form:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
    }

    #heading {
        text-align: center;
        margin: 1.2em 0 1.5em;
        color: white;
        font-size: 2em;
        font-weight: 800;
        letter-spacing: 0.5px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .field {
        display: flex;
        align-items: center;
        gap: 0.5em;
        border-radius: 20px;
        padding: 0.9em;
        background-color: #1f1f1f;
        box-shadow: inset 2px 5px 10px rgb(5, 5, 5);
        margin-bottom: 15px;
        transition: all 0.3s;
        border: 1px solid #2a2a2a;
    }

    .field:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2), inset 2px 5px 10px rgb(5, 5, 5);
    }

    .input-icon {
        height: 1.3em;
        width: 1.3em;
        fill: #6366f1;
        transition: all 0.3s;
    }

    .field:focus-within .input-icon {
        fill: #8b5cf6;
        transform: scale(1.1);
    }

    .input-field {
        background: none;
        border: none;
        outline: none;
        width: 100%;
        color: #f0f0f0;
        font-size: 1em;
        padding: 5px 0;
    }

    .input-field::placeholder {
        color: #888;
    }

    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 2.2em;
    }

    .button-main {
        flex: 1;
        padding: 0.9em;
        border-radius: 12px;
        border: none;
        background-color: #252525;
        color: white;
        cursor: pointer;
        font-weight: 700;
        transition: all 0.3s;
        font-size: 1em;
        letter-spacing: 0.5px;
    }

    .button-main:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 14px rgba(0, 0, 0, 0.2);
    }

    .button-main:first-child {
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
    }

    .button-main:first-child:hover {
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
    }

    .link-forgot {
        text-align: center;
        color: #888;
        font-size: 0.85em;
        text-decoration: none;
        margin-top: 2em;
        transition: .3s;
        font-weight: 500;
    }

    .link-forgot:hover {
        color: #ef4444;
    }

    .error-txt {
        color: #ef4444;
        font-size: 0.8em;
        text-align: center;
        margin-bottom: 10px;
        padding: 8px;
        background-color: rgba(239, 68, 68, 0.1);
        border-radius: 8px;
    }

    /* Animasi kucing */
    .loader {
        width: fit-content;
        height: fit-content;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wrapper {
        width: fit-content;
        height: fit-content;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .catContainer {
        width: 100%;
        height: fit-content;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .catbody {
        width: 80px;
    }

    .tail {
        position: absolute;
        width: 17px;
        top: 50%;
        animation: tail 0.5s ease-in infinite alternate-reverse;
        transform-origin: top;
    }

    @keyframes tail {
        0% {
            transform: rotateZ(60deg);
        }

        50% {
            transform: rotateZ(0deg);
        }

        100% {
            transform: rotateZ(-20deg);
        }
    }

    .wall {
        width: 300px;
    }

    .wall line {
        stroke-width: 6;
        stroke: #7C7C7C;
    }

    .text {
        display: flex;
        flex-direction: column;
        width: 50px;
        position: absolute;
        margin: 0px 0px 100px 120px;
    }

    .zzz {
        color: black;
        font-weight: 700;
        font-size: 15px;
        animation: zzz 2s linear infinite;
    }

    .bigzzz {
        color: black;
        font-weight: 700;
        font-size: 25px;
        margin-left: 10px;
        animation: zzz 2.3s linear infinite;
    }

    @keyframes zzz {
        0% {
            color: transparent;
        }

        50% {
            color: black;
        }

        100% {
            color: transparent;
        }
    }

    @media (max-width: 480px) {
        .form {
            padding: 2em 1.5em;
        }

        .cat-animation-container {
            transform: scale(0.7);
        }

        #heading {
            font-size: 1.8em;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Form Login -->
        <form class="form" action="{{ route('login.submit') }}" method="POST">
            @csrf
            <p id="heading">✎ᝰ.📓🗒 ˎˊ˗<br>
                Porto Tracking</p>

            @if($errors->any())
            <div class="error-txt">⚠️ {{ $errors->first() }}</div>
            @endif

            <div class="field">
                <svg class="input-icon" viewBox="0 0 16 16">
                    <path
                        d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177 .704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z">
                    </path>
                </svg>
                <input placeholder="Email" class="input-field" type="email" name="email" value="{{ old('email') }}"
                    required>
            </div>

            <div class="field">
                <svg class="input-icon" viewBox="0 0 16 16">
                    <path
                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z">
                    </path>
                </svg>
                <input placeholder="Password" class="input-field" type="password" name="password" required>
            </div>

            <div class="btn-group">
                <button type="submit" class="button-main">Login</button>
                <a href="{{ route('register') }}" style="text-decoration:none" class="button-main">Daftar</a>
            </div>

            <a href="{{ route('password.request') }}" class="link-forgot">Lupa Password?</a>
        </form>

        <!-- Animasi Kucing Tidur di Pagar -->
        <div class="cat-animation-container">
            <div class="cat-wrapper">
                <div class="catContainer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 733 673" class="catbody">
                        <path fill="#212121"
                            d="M111.002 139.5C270.502 -24.5001 471.503 2.4997 621.002 139.5C770.501 276.5 768.504 627.5 621.002 649.5C473.5 671.5 246 687.5 111.002 649.5C-23.9964 611.5 -48.4982 303.5 111.002 139.5Z">
                        </path>
                        <path fill="#212121" d="M184 9L270.603 159H97.3975L184 9Z"></path>
                        <path fill="#212121" d="M541 0L627.603 150H454.397L541 0Z"></path>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 158 564" class="tail">
                        <path fill="#191919"
                            d="M5.97602 76.066C-11.1099 41.6747 12.9018 0 51.3036 0V0C71.5336 0 89.8636 12.2558 97.2565 31.0866C173.697 225.792 180.478 345.852 97.0691 536.666C89.7636 553.378 73.0672 564 54.8273 564V564C16.9427 564 -5.4224 521.149 13.0712 488.085C90.2225 350.15 87.9612 241.089 5.97602 76.066Z">
                        </path>
                    </svg>
                    <div class="text">
                        <span class="bigzzz">Z</span>
                        <span class="zzz">Z</span>
                    </div>
                </div>
                <div class="wallContainer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 500 126" class="wall">
                        <line stroke-width="6" stroke="#7C7C7C" y2="3" x2="450" y1="3" x1="50"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="85" x2="400" y1="85" x1="100"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="122" x2="375" y1="122" x1="125"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="43" x2="500" y1="43"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="1.99391" x2="115.5" y1="43.0061" x1="115.5"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="2.00002" x2="189" y1="43.0122" x1="189"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="2.00612" x2="262.5" y1="43.0183" x1="262.5"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="2.01222" x2="336" y1="43.0244" x1="336"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="2.01833" x2="409.5" y1="43.0305" x1="409.5"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="43" x2="153" y1="84.0122" x1="153"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="43" x2="228" y1="84.0122" x1="228"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="43" x2="303" y1="84.0122" x1="303"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="43" x2="378" y1="84.0122" x1="378"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="84" x2="192" y1="125.012" x1="192"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="84" x2="267" y1="125.012" x1="267"></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="84" x2="342" y1="125.012" x1="342"></line>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Interaksi form dan animasi
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.form');
        const fields = document.querySelectorAll('.field');
        const catContainer = document.querySelector('.catContainer');
        const tail = document.querySelector('.tail');

        // Efek saat form di-hover
        form.addEventListener('mouseenter', function() {
            catContainer.style.transform = 'translateY(-5px)';
            catContainer.style.transition = 'transform 0.3s ease';
        });

        form.addEventListener('mouseleave', function() {
            catContainer.style.transform = 'translateY(0)';
        });

        // Efek saat input di-focus
        fields.forEach(field => {
            const input = field.querySelector('.input-field');

            input.addEventListener('focus', function() {
                // Ekor kucing bergerak lebih aktif
                tail.style.animationDuration = '0.5s';
                // "Zzz" lebih cepat
                const zzzElements = document.querySelectorAll('.zzz, .bigzzz');
                zzzElements.forEach(el => {
                    el.style.animationDuration = '1.5s';
                });
            });

            input.addEventListener('blur', function() {
                // Kembali ke animasi normal
                tail.style.animationDuration = '0.8s';
                const zzzElements = document.querySelectorAll('.zzz, .bigzzz');
                zzzElements.forEach(el => {
                    el.style.animationDuration = '2.2s';
                });
            });
        });

        // Validasi form sederhana
        form.addEventListener('submit', function(e) {
            const email = form.querySelector('input[name="email"]').value;
            const password = form.querySelector('input[name="password"]').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Harap isi email dan password.');
                return false;
            }

            // Simulasi loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Loading...';
            submitBtn.disabled = true;

            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    });
    </script>
</body>

</html>