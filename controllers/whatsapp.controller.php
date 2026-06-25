<?php

class WhatsAppController {

    private static $apiUrl   = "https://graph.facebook.com/v25.0/";
    private static $phoneId  = "947125511257489";   // De Meta Business
    private static $token    = "EAAVIaiawwSMBRyIQzUxVlbOiCQby4ichgFBZCrXEDIRZCFK0obT7JXdjgozX5STZB09yp7TYKbTyOLhtOlMEax4AuNCwVq9FlMV07U2IfsyX5EqNtBPquKecZBIruy2HGagLTnicaZBoQZAiMe81SSxO7r5tHUa1Ej13UuxFxNHt5xkD66OldSisyGwK5KCgZDZD";       // Token permanente
/*EAAiE5pNltcgBRQ2FRHTFTUXygH39v2dl4eL8YdKhxp7xsJMyVv4ojUVYRRuwMSBDcKM8u1GfdnRjm98wTZA4TNjG2v9a4u28wpXiriBFBSztgUdTiaeW2KkjExZAAoMBEGRqZCBhdOkRpXhTZAZBBbeERudfJ0bqIU3d3AZAcL3FiZB3IhcJWiYsIH9auFVAtgDbyFnc8tLJPtR80eOBExoaAYx7kQmMG1GLXVpEZBB1nPEuoQorLT3RguMwtZCyT7CqTZA2ZCbJ4qejFvIyzqRKZBDdhAZDZD*/
    /**
     * Envía mensaje de confirmación de reserva por WhatsApp
     *
     * @param string $phone     Número destino (formato internacional sin + ej: 573001234567)
     * @param array  $bookData  Datos de la reserva
     * @return object
     */
    public static function sendBookingConfirmation(string $phone, array $bookData): object {

    $phone = self::formatPhone($phone);
    
    // LOG: Ver qué número se está formateando
    error_log("WhatsApp DEBUG - Phone original: " . $bookData['client']);
    error_log("WhatsApp DEBUG - Phone formateado: " . $phone);
    error_log("WhatsApp DEBUG - Phone ID: " . self::$phoneId);
    error_log("WhatsApp DEBUG - URL: " . self::$apiUrl . self::$phoneId . "/messages");

    $body = [
        "messaging_product" => "whatsapp",
        "to"                => $phone,
        "type"              => "text",
        "text"              => [
            "body" => self::buildMessage($bookData)
        ]
    ];

    // LOG: Ver el body que se envía
    error_log("WhatsApp DEBUG - Body: " . json_encode($body));

    $ch = curl_init(self::$apiUrl . self::$phoneId . "/messages");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer " . self::$token,
            "Content-Type: application/json"
        ],
        CURLOPT_SSL_VERIFYPEER => false, // Por si hay problema de SSL
        CURLOPT_VERBOSE        => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // LOG: Ver respuesta completa
    error_log("WhatsApp DEBUG - HTTP Code: " . $httpCode);
    error_log("WhatsApp DEBUG - cURL Error: " . ($curlError ?: 'ninguno'));
    error_log("WhatsApp DEBUG - Response: " . $response);

    return (object)[
        "status"   => $httpCode,
        "response" => json_decode($response)
    ];
}
    /**
     * Construye el mensaje de confirmación
     */
    private static function buildMessage(array $d): string {

        return "✅ *Confirmación de Reserva*\n\n"
             . "👤 *Cliente:* {$d['client']}\n"
             . "📅 *Fecha:* {$d['date']}\n"
             . "🕐 *Hora:* {$d['time']}\n"
             . "💆 *Especialista:* {$d['specialist']}\n"
             . "🔢 *N° Reserva:* #{$d['num']}\n\n"
             . "Si necesitas cancelar o modificar tu cita, comunícate con nosotros.\n"
             . "¡Te esperamos! 🙏";
    }

    /**
     * Normaliza el número al formato internacional (Colombia por defecto)
     */
    private static function formatPhone(string $phone): string {

        // Elimina espacios, guiones, paréntesis
        $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);

        // Si empieza con 0, lo quita
        $phone = ltrim($phone, '0');

        // Si no tiene código de país, asume Colombia (+57)
        if (strlen($phone) === 10 && $phone[0] === '3') {
            $phone = '57' . $phone;
        }

        return $phone;
    }
}