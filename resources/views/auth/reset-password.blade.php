<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Porto Tracking</title>
    <style>
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
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 1200px;
        padding: 40px;
    }

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
        margin: 0 0 0.5em;
        color: white;
        font-size: 2em;
        font-weight: 800;
        letter-spacing: 0.5px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .subtitle {
        text-align: center;
        color: #888;
        font-size: 0.9em;
        margin-bottom: 1.5em;
        font-weight: 500;
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

    .button-main {
        width: 100%;
        padding: 0.9em;
        border-radius: 12px;
        border: none;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        color: white;
        cursor: pointer;
        font-weight: 700;
        transition: all 0.3s;
        font-size: 1em;
        letter-spacing: 0.5px;
        margin-top: 0.5em;
    }

    .button-main:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 14px rgba(99, 102, 241, 0.4);
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
    }

    .link-back {
        text-align: center;
        color: #888;
        font-size: 0.85em;
        text-decoration: none;
        margin-top: 1.5em;
        transition: .3s;
        font-weight: 500;
        display: block;
    }

    .link-back:hover {
        color: #ef4444;
    }

    .message {
        padding: 10px;
        border-radius: 12px;
        margin-bottom: 1.5em;
        text-align: center;
        font-size: 0.9em;
        font-weight: 500;
    }

    .message.error {
        background-color: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    @media (max-width: 480px) {
        .form {
            padding: 2em 1.5em;
            width: 90%;
        }

        #heading {
            font-size: 1.8em;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <form class="form" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <p id="heading">Porto Tracking</p>
            <p class="subtitle">Reset Password</p>

            @if ($errors->any())
            <div class="message error">
                ⚠️ {{ $errors->first() }}
            </div>
            @endif

            <div class="field">
                <svg class="input-icon" viewBox="0 0 16 16">
                    <path
                        d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177 .704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z">
                    </path>
                </svg>
                <input placeholder="Email" class="input-field" type="email" name="email"
                    value="{{ $email ?? old('email') }}" required>
            </div>

            <div class="field">
                <svg class="input-icon" viewBox="0 0 16 16">
                    <path
                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z">
                    </path>
                </svg>
                <input placeholder="Password Baru" class="input-field" type="password" name="password" required>
            </div>

            <div class="field">
                <svg class="input-icon" viewBox="0 0 16 16">
                    <path
                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z">
                    </path>
                </svg>
                <input placeholder="Konfirmasi Password Baru" class="input-field" type="password"
                    name="password_confirmation" required>
            </div>

            <button type="submit" class="button-main">Reset Password</button>

            <a href="{{ route('login') }}" class="link-back">← Kembali ke Login</a>
        </form>
    </div>
</body>

</html>