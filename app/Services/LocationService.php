<?php

namespace App\Services;

class LocationService
{
    /**
     * Calcular distância entre duas coordenadas usando fórmula de Haversine
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Raio da Terra em quilômetros

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c);
    }

    /**
     * Obter coordenadas de uma localização usando geocoding
     */
    public function geocodeLocation($location)
    {
        try {
            // Usar OpenStreetMap Nominatim (gratuito, sem chave API)
            $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($location) . "&limit=1";

            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'ApplyFlow/1.0'
                ]
            ]);

            $response = file_get_contents($url, false, $context);

            if ($response) {
                $data = json_decode($response, true);

                if (!empty($data)) {
                    return [
                        'latitude' => (float) $data[0]['lat'],
                        'longitude' => (float) $data[0]['lon'],
                        'city' => $this->extractCity($data[0]),
                        'country' => $data[0]['display_name'] ?? null
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error silently
        }

        return null;
    }

    /**
     * Extrair nome da cidade dos dados do geocoding
     */
    private function extractCity($locationData)
    {
        // Tentar diferentes campos para obter o nome da cidade
        $cityFields = ['city', 'town', 'village', 'municipality', 'county'];

        foreach ($cityFields as $field) {
            if (isset($locationData[$field])) {
                return $locationData[$field];
            }
        }

        // Fallback: usar o display_name e extrair a primeira parte
        if (isset($locationData['display_name'])) {
            $parts = explode(',', $locationData['display_name']);
            return trim($parts[0]);
        }

        return null;
    }

    /**
     * Detectar idioma baseado na localização
     */
    public function detectLanguageFromLocation($country, $city = null)
    {
        $languageMap = [
            'Brazil' => 'pt',
            'Portugal' => 'pt',
            'Spain' => 'es',
            'Mexico' => 'es',
            'Argentina' => 'es',
            'Colombia' => 'es',
            'Chile' => 'es',
            'Peru' => 'es',
            'United States' => 'en',
            'United Kingdom' => 'en',
            'Canada' => 'en',
            'Australia' => 'en',
            'New Zealand' => 'en',
            'Germany' => 'de',
            'France' => 'fr',
            'Italy' => 'it',
            'Netherlands' => 'nl',
        ];

        // Verificar país primeiro
        if (isset($languageMap[$country])) {
            return $languageMap[$country];
        }

        // Verificar se contém palavras-chave nos nomes das cidades
        $spanishCities = ['Madrid', 'Barcelona', 'Buenos Aires', 'Mexico City', 'Santiago', 'Lima', 'Bogotá'];
        $portugueseCities = ['São Paulo', 'Rio de Janeiro', 'Lisboa', 'Porto', 'Brasília', 'Salvador'];

        if ($city && in_array($city, $spanishCities)) {
            return 'es';
        }

        if ($city && in_array($city, $portugueseCities)) {
            return 'pt';
        }

        // Default para inglês
        return 'en';
    }

    /**
     * Obter localização do usuário baseada no IP (fallback)
     */
    public function getLocationFromIP()
    {
        try {
            $ip = request()->ip();

            // Usar ipapi.co (gratuito)
            $url = "http://ipapi.co/{$ip}/json/";

            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'user_agent' => 'ApplyFlow/1.0'
                ]
            ]);

            $response = file_get_contents($url, false, $context);

            if ($response) {
                $data = json_decode($response, true);

                if (isset($data['latitude']) && isset($data['longitude'])) {
                    return [
                        'latitude' => (float) $data['latitude'],
                        'longitude' => (float) $data['longitude'],
                        'city' => $data['city'] ?? null,
                        'country' => $data['country_name'] ?? null
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error silently
        }

        return null;
    }
}