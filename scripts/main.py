import paho.mqtt.client as mqtt
import json
import time
from datetime import datetime, timezone

# --- Configuration ---
BROKER = "spaceship.azizozk.com"
PORT = 1883
SERIAL_NUMBER = "CC1XXXXXXXX"
TOPIC = f"uagv/v2/PuduRobotics/{SERIAL_NUMBER}/state"

# --- VDA 5050 Sample Payload ---
payload = {
    "headerId": 101,
    "timestamp": datetime.now(timezone.utc).isoformat(timespec='seconds'),
    "manufacturer": "PuduRobotics",
    "serialNumber": SERIAL_NUMBER,
    "agvPosition": {
        "x": 5.23,
        "y": -1.45,
        "theta": 0.52,
        "mapId": "Office_Floor_1"
    },
    "batteryState": {
        "batteryLevel": 85.0,
        "charging": False
    }
}


def on_publish(client, userdata, mid):
    print(f"✅ Message {mid} successfully published to {TOPIC}")


# Initialize Client
client = mqtt.Client()
client.on_publish = on_publish

try:
    print(f"Connecting to {BROKER} on port {PORT}...")
    client.connect(BROKER, PORT, 60)
    client.loop_start()

    # Send the message
    result = client.publish(TOPIC, json.dumps(payload), qos=1)
    result.wait_for_publish(timeout=10)

    print("Payload Sent:")
    print(json.dumps(payload, indent=2))

except Exception as e:
    print(f"❌ Failed to send message: {e}")

finally:
    client.loop_stop()
    client.disconnect()