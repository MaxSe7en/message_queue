# Message Queue Project

This project is a **PHP & Java** backend application that utilizes **FastRoute** for routing, **PDO** for database operations, and **Kafka** for message queuing. 

## 🚀 Features
- **FastRoute** for efficient routing
- **MySQL Database Connection** using PDO
- **CRUD Operations** (Create, Read, Update, Delete)
- **Kafka Integration** for message queuing

---


---

## 🛠 Installation

### 1️⃣ Clone the Repository
```sh
git clone https://github.com/MaxSe7en/message_queue.git
cd message_queue
```

### 2️⃣ Install Dependencies
```sh
composer install
```

### 3️⃣ Setup Environment Variables
Create a `.env` file in the root directory:
```
DB_HOST=localhost
DB_NAME=your_database
DB_USER=root
DB_PASS=your_password
KAFKA_BROKER=localhost:9092
```

### 4️⃣ Start Apache & MySQL (If not already running)
- Ensure **Apache** and **MySQL** are running (via XAMPP, MAMP, or standalone services).

### 5️⃣ Setup Database
```sql
CREATE TABLE momo_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🚦 Running the Application

### 🏃 Start the PHP Server
```sh
php -S localhost:8000 -t public
```

### 📌 Available Routes
| Method | Route                         | Description |
|--------|-------------------------------|-------------|
| GET    | `/`                            | Home Page |
| POST   | `/api/v1/add_momo_transaction` | Insert Transaction |

---

## 📡 Kafka Integration

### 1️⃣ Install Kafka & Start Services
```sh
bin/zookeeper-server-start.sh config/zookeeper.properties
bin/kafka-server-start.sh config/server.properties
```

### 2️⃣ Run Kafka Producer
```php
$producer = new KafkaProducer("momo_transactions");
$producer->sendMessage("New mobile money transaction received");
```

### 3️⃣ Run Kafka Consumer
```php
$consumer = new KafkaConsumer("momo_transactions");
$consumer->listen();
```

# 📌 Kafka Consumer Configuration Explained

## 🔹 Key Configuration Options

| Config Key                 | Description |
|----------------------------|-------------|
| `metadata.broker.list`      | Specifies the Kafka broker address (`localhost:9092` or another broker). This tells the consumer where to find Kafka. |
| `group.id`                 | A unique ID for the consumer group. Consumers with the same group ID will share the load of processing messages from partitions. |
| `auto.offset.reset`        | Determines where the consumer should start reading if no previous offset is stored: |
|                            | - `earliest`: Start reading from the beginning of the topic. |
|                            | - `latest`: Start reading from the newest messages. |
|                            | - `none`: Fail if no offset is stored. |
| `enable.auto.commit`       | If set to `true`, Kafka automatically saves the last read offset. If `false`, you must manually commit offsets. |
| `fetch.min.bytes`          | The minimum amount of data the consumer will receive in one fetch request. Helps optimize performance. |
| `max.poll.records`         | The maximum number of messages a consumer will fetch in a single poll request. |
| `session.timeout.ms`       | The time (in milliseconds) the broker waits before assuming a consumer is dead if it doesn't send heartbeats. |
| `heartbeat.interval.ms`    | The frequency at which the consumer sends heartbeats to the broker to indicate it's still alive. |

---

## 🛠️ Example Configuration in PHP

```php
$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'localhost:9092'); // Set Kafka broker
$conf->set('group.id', 'my_consumer_group'); // Set consumer group ID
$conf->set('auto.offset.reset', 'earliest'); // Read from beginning if no offset exists
$conf->set('enable.auto.commit', 'false'); // Manually commit offsets
$conf->set('session.timeout.ms', '60000'); // Set session timeout
$conf->set('heartbeat.interval.ms', '5000'); // Send heartbeats every 5s

$consumer = new RdKafka\Consumer($conf);

---

## 📝 Author
- **Awuah Twerefour Maxwell**
- GitHub: MaxSe7en(https://github.com/MaxSe7en)
- Email: nanayawfixing@gmail.com

---

## ⚖️ License
This project is licensed under the **MIT License**.
