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

---

## 📝 Author
- **Awuah Twerefour Maxwell**
- GitHub: MaxSe7en(https://github.com/MaxSe7en)
- Email: nanayawfixing@gmail.com

---

## ⚖️ License
This project is licensed under the **MIT License**.
