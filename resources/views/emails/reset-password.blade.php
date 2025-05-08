<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera tu contraseña - Johny Motorbike</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        /* Encabezado */
        .header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            padding: 20px;
            text-align: center;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .logo-image {
            height: 40px;
            width: auto;
            vertical-align: middle;
        }
        
        .logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
            display: inline-block;
            vertical-align: middle;
        }
        
        .logo-accent {
            color: #93c5fd;
            font-style: italic;
        }
        
        /* Contenido */
        .content {
            padding: 30px;
            background-color: #ffffff;
        }
        
        h2 {
            color: #1e40af;
            margin-top: 0;
            font-size: 22px;
        }
        
        p {
            margin: 20px 0;
            color: #4b5563;
        }
        
        /* Botón de acción */
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.25);
            transition: transform 0.2s;
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(37, 99, 235, 0.35);
        }
        
        /* Detalles y footer */
        .details {
            background-color: #f1f5f9;
            padding: 20px;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        
        .note {
            color: #6b7280;
            font-size: 13px;
            font-style: italic;
            text-align: center;
            margin: 15px 0;
        }
        
        .footer {
            background-color: #1e3a8a;
            color: #93c5fd;
            text-align: center;
            padding: 15px;
            font-size: 12px;
        }

        .footer-p {
            margin: 5px 0;
            color: #d3d3d3;
        }
        
        .separator {
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
        
        /* Elementos decorativos */
        .motorcycle-icon {
            font-size: 24px;
            margin: 0 5px;
        }
        
        .warning {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
            padding: 10px 15px;
            margin: 20px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            .content, .details {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <!-- Logo incluido como imagen que apunta a un recurso público -->
                
                <h1 class="logo">JOHNY <span class="logo-accent">MOTORBIKE</span></h1>
            </div>
        </div>
        
        <div class="content">
            <h2>¡Hola {{ $name ?? 'motociclista' }}!</h2>
            
            <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en Johny Motorbike, la comunidad de rutas para motociclistas.</p>
            
            <p>Si no solicitaste este cambio, simplemente ignora este mensaje y tu contraseña seguirá siendo la misma.</p>
            
            <div class="button-container">
                <a href="{{ $url }}" class="button">Restablecer mi contraseña</a>
            </div>
            
            <p>Si tienes problemas para hacer clic en el botón "Restablecer mi contraseña", copia y pega la siguiente URL en tu navegador web:</p>
            <p style="word-break: break-all; font-size: 14px; color: #6b7280;">{{ $url }}</p>
            
            <div class="warning">
                <strong>Importante:</strong> Este enlace expirará en {{ $count ?? 60 }} minutos por razones de seguridad.
            </div>
        </div>
        
        <div class="details">
            <p style="margin-top: 0;">Si no solicitaste restablecer tu contraseña, te recomendamos:</p>
            <ul>
                <li>Verificar la seguridad de tu cuenta</li>
                <li>Cambiar tu contraseña si sospechas que alguien más ha intentado acceder</li>
                <li>Contactar con nuestro equipo de soporte si necesitas ayuda</li>
            </ul>
            
            <div class="separator"></div>
            
            <p class="note">Este mensaje ha sido enviado automáticamente. Por favor no respondas a este correo.</p>
        </div>
        
        <div class="footer">
            <p class="footer-p">© {{ date('Y') }} Johny Motorbike. Todos los derechos reservados.</p>
            <p class="footer-p">Las mejores rutas para tu aventura en dos ruedas</p>
        </div>
    </div>
</body>
</html>