#include <ESP8266WiFi.h>

#include <PubSubClient.h>

#include <AccelStepper.h> //library motor stepper

#define HALFSTEP 8        // definisi jumlah step

// definisi pin Arduino pada driver motor

#define motorPin1 5 // IN1 pada ULN2003 driver 1
#define motorPin2 4 // IN2 pada ULN2003 driver 1
#define motorPin3 0 // IN3 pada ULN2003 driver 1
#define motorPin4 2 // IN4 pada ULN2003 driver 1

// inisiasi urutan pin IN1-IN3-IN2-IN4 untuk library AccelStepper dengan motor 28BYJ-48

AccelStepper stepper(HALFSTEP, motorPin1, motorPin3, motorPin2, motorPin4);

WiFiClient espClient;
PubSubClient client(espClient);

void reconnectmqttserver() {
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    String clientId = "phpMQTT-publisher";
    clientId += String(random(0xffff), HEX);
    if (client.connect(clientId.c_str())) {
      Serial.println("connected");
      client.subscribe("bluerhinos/phpMQTT/examples/pagar");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      delay(5000);
    }
  }
}

void callback(char* topic, byte* payload, unsigned int length) {
  String MQTT_DATA = "";
  for (int i = 0; i < length; i++) {
    MQTT_DATA += (char)payload[i];
  }
  if (MQTT_DATA == "on") {
    //    digitalWrite(2,HIGH);
    stepper.runToNewPosition(2000);
  }
  if (MQTT_DATA == "off") {
    //    digitalWrite(2, LOW);
    stepper.runToNewPosition(-2000);
  }

  if (MQTT_DATA == "lampuon") {
    digitalWrite(14, HIGH);
  }
  if (MQTT_DATA == "lampuoff") {
    digitalWrite(14, LOW);
  }


  if (MQTT_DATA == "tvon") {
    digitalWrite(12, HIGH);
  }
  if (MQTT_DATA == "tvoff") {
    digitalWrite(12, LOW);
  }


  if (MQTT_DATA == "kipason") {
    digitalWrite(13, HIGH);
  }
  if (MQTT_DATA == "kipasoff") {
    digitalWrite(13, LOW);
  }

  if (MQTT_DATA == "pompaon") {
    digitalWrite(15, HIGH);
  }
  if (MQTT_DATA == "pompaoff") {
    digitalWrite(15, LOW);
  }

}

void setup()
{
  Serial.begin(9600);
  stepper.setMaxSpeed(1000.0);    //setting kecepatan maksimal motor
  stepper.setAcceleration(200.0); //setting akselerasi
  stepper.setSpeed(200);            //setting kecepatan
  WiFi.disconnect();
  delay(3000);
  Serial.println("START");
  WiFi.begin("Wifi Dapur", "satuduatiga");
  while ((!(WiFi.status() == WL_CONNECTED))) {
    delay(300);
    Serial.print("..");

  }
  Serial.println("Connected");
  Serial.println("Your IP is");
  Serial.println((WiFi.localIP().toString()));
  client.setServer("47.254.198.43", 1883);
  client.setCallback(callback);

  pinMode(2, OUTPUT);
  pinMode(14, OUTPUT);
  pinMode(12, OUTPUT);
  pinMode(13, OUTPUT);
  pinMode(15, OUTPUT);
}


void loop()
{

  if (!client.connected()) {
    reconnectmqttserver();
  }
  client.loop();

}
