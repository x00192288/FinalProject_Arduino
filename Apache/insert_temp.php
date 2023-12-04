/*
 * Created by ArduinoGetStarted.com
 *
 * This example code is in the public domain
 *
 * Tutorial page: https://arduinogetstarted.com/tutorials/arduino-mysql
 */

#include <SPI.h>
#include <WiFiNINA.h>
#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>
#include <Arduino_LSM6DS3.h>


#include "arduino_secrets.h" 
char ssid[] = SECRET_SSID;        // your network SSID (name)
char pass[] = SECRET_PASS;    // your network password (use for WPA, or use as key for WEP)
int keyIndex = 0;                 // your network key index number (needed only for WEP)

int status = WL_IDLE_STATUS;
//
//IPAddress server_addr(192,168,1,5);  // IP of the MySQL *server* here
//char user[] = "Arduino";              // MySQL user login username
//char password[] = "x0192288!";   

WiFiServer server(80);

WiFiClient client;


int    HTTP_PORT   = 80;
String HTTP_METHOD = "GET";
char   HOST_NAME[] = "172.20.10.3"; // change to your PC's IP address
String PATH_NAME   = "/insert_temp.php";
String queryString = "?temperature=44";

void setup() {
  Serial.begin(9600);

  // initialize the Ethernet shield using DHCP:
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
  // you're connected now, so print out the status:
  printWifiStatus();


  // you're connected now, so print out the data:
  Serial.println("You're connected to the network");



// move this to loop
  if (client.connect(HOST_NAME, 80))
  {
    // REPLACE WITH YOUR SERVER ADDRESS
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
  //printCurrentNet();
  //printWifiData();

//  // connect to web server on port 80:
// if(client.connect(HOST_NAME, HTTP_PORT)) {
//    // if connected:
//    Serial.println("Connected to server");
//    // make a HTTP request:
//    // send HTTP header
//    client.println(HTTP_METHOD + " " + PATH_NAME + queryString + " HTTP/1.1");
//    client.println("Host: " + String(HOST_NAME));
//    client.println("Connection: close");
//    client.println(); // end HTTP header
//
//    while(client.connected()) {
//      if(client.available()){
//        // read an incoming byte from the server and print it to serial monitor:
//        char c = client.read();
//        Serial.print(c);
//      }
//    }
//
//    // the server's disconnected, stop the client:
//    client.stop();
//    Serial.println();
//    Serial.println("disconnected");
//  } else {// if not connected:
//    Serial.println("connection failed");
//  }
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
//int temperature;
//temperature = 1;
//
//client.print(temp);
  client.print(queryString);
  
unsigned long previousMillis = 0;
const unsigned long interval = 10000; // 1 second interval

while (1) {
  unsigned long currentMillis = millis();

  if (currentMillis - previousMillis >= interval) {
    // Your code here
float t;

  if (IMU.temperatureAvailable()) {
    // after IMU.readTemperature() returns, t will contain the temperature reading
    
    IMU.readTemperature(t);


    Serial.println(t);
    // write to db here instead of printing
  }
    // Update the previousMillis to the current time
    previousMillis = currentMillis;
  }
  
}
}


void printWifiStatus() {
  // print the SSID of the network you're attached to:
  Serial.print("SSID: ");
  Serial.println(WiFi.SSID());

  // print your board's IP address:
  IPAddress ip = WiFi.localIP();
  Serial.print("IP Address: ");
  Serial.println(ip);

  // print the received signal strength:
  long rssi = WiFi.RSSI();
  Serial.print("signal strength (RSSI):");
  Serial.print(rssi);
  Serial.println(" dBm");
}
