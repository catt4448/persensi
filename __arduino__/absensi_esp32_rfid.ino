#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN  5
#define RST_PIN 22

const char* ssid = "WIFI_SSID";
const char* password = "WIFI_PASS";
const char* apiUrl = "http://YOUR_SERVER_IP/api/absensi/scan";
const char* deviceId = "ESP32-01";
const char* deviceToken = "DEVICE_TOKEN";

MFRC522 rfid(SS_PIN, RST_PIN);

String uidToString() {
  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}

void setup() {
  Serial.begin(115200);
  SPI.begin();
  rfid.PCD_Init();

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");
}

void loop() {
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    delay(200);
    return;
  }

  String uid = uidToString();
  Serial.println("UID: " + uid);

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(apiUrl);
    http.addHeader("Content-Type", "application/json");

    String payload = "{\"uid\":\"" + uid + "\",\"device_id\":\"" + String(deviceId) + "\",\"token\":\"" + String(deviceToken) + "\"}";
    int httpResponseCode = http.POST(payload);

    String response = http.getString();
    Serial.println("HTTP: " + String(httpResponseCode));
    Serial.println("Response: " + response);

    http.end();
  }

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  delay(1500);
}
