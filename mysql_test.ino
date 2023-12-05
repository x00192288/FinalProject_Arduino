#include <SPI.h>
#include <WiFiNINA.h>
#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>
#include <Arduino_LSM6DS3.h>


#include "arduino_secrets.h" 
char ssid[] = SECRET_SSID;        //  network SSID (name)
char pass[] = SECRET_PASS;    //  network password (use for WPA, or use as key for WEP)
int keyIndex = 0;                 //  network key index number (needed only for WEP)

int status = WL_IDLE_STATUS; 

WiFiServer server(80);

WiFiClient client;


int    HTTP_PORT   = 80;
String HTTP_METHOD = "GET";
char   HOST_NAME[] = "172.20.10.3"; // change to your PC's IP address
String PATH_NAME   = "/insert_temp.php";
//String queryString = "?temperature=44";

void setup() {
  Serial.begin(9600);

  // initialize Wifi
  while (!Serial) {
    ; // wait for serial port to connect. Needed for native USB port only
  }

  // check for the WiFi module:
  if (WiFi.status() == WL_NO_MODULE) {
    Serial.println("Communication with WiFi module failed!");
    // don't continue
    while (true);
  }

  String fv = WiFi.firmwareVersion();
  if (fv < WIFI_FIRMWARE_LATEST_VERSION) {
    Serial.println("Please upgrade the firmware");
  }

  // attempt to connect to WiFi network:
  while (status != WL_CONNECTED) {
    Serial.print("Attempting to connect to WPA SSID: ");
    Serial.println(ssid);
    // Connect to WPA/WPA2 network:
    status = WiFi.begin(ssid, pass);

    // wait 10 seconds for connection:
    delay(10000);
  }
  server.begin();
  // connected now, print out the status:
  printWifiStatus();


  // connected now, print out the data:
  Serial.println("You're connected to the network");


if (!IMU.begin()) {
    Serial.println("Failed to initialize IMU!");

    while (1);
  }

  Serial.print("Temperature sensor sample rate = ");
  Serial.print(IMU.temperatureSampleRate());
  Serial.println(" Hz");
  Serial.println();
  Serial.println("Temperature reading in degrees C");
  Serial.println("T");
}

void loop() {
  
unsigned long previousMillis = 0;
const unsigned long interval = 600000; // 60 second interval

while (1) {
  unsigned long currentMillis = millis();

  if (currentMillis - previousMillis >= interval) {
float t;

  if (IMU.temperatureAvailable()) {
    // after IMU.readTemperature() returns, t will contain the temperature reading
    
    IMU.readTemperature(t);


    Serial.println(t);
    if (client.connect(HOST_NAME, 80))
  {
    String queryString = "?temperature=" + String(t);

    Serial.println("connected");
    client.println(HTTP_METHOD + " " + PATH_NAME + queryString + " HTTP/1.1");
    Serial.println("method correct");

    client.println("Host: " + String(HOST_NAME));
        Serial.println("host correct");

    client.println("Connection: close");
        Serial.println("closing connection");

    client.println(); // end HTTP header
    
     while(client.connected()) {
                Serial.println("client still available");

      if (client.available()) {
    char c = client.read();
    Serial.print(c);
  }
      
      else{
                Serial.println("breaking");

        break;
      }
    }

    // the server's disconnected, stop the client:
    client.stop();
    Serial.println();
    Serial.println("disconnected");
  } else {// if not connected:
    Serial.println("connection failed");
  }
  }
    // Update the previousMillis to the current time
    previousMillis = currentMillis;
  }
  



  






}
}// LOOP


void printWifiStatus() {
  // print the SSID of the network you're attached to:
  Serial.print("SSID: ");
  Serial.println(WiFi.SSID());

  // print board's IP address:
  IPAddress ip = WiFi.localIP();
  Serial.print("IP Address: ");
  Serial.println(ip);

  // print the received signal strength:
  long rssi = WiFi.RSSI();
  Serial.print("signal strength (RSSI):");
  Serial.print(rssi);
  Serial.println(" dBm");
}
