# 🏥 MedyVend - Medicine Vending Machine Ecosystem

[![Laravel Version](https://img.shields.io/badge/Laravel-8.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen.svg)](https://github.com)

**MedyVend** is an intelligent IoT-enabled medicine vending machine ecosystem that provides automated, secure, and traceable medication dispensing for healthcare facilities, pharmacies, and public health centers.

---

## 📋 Table of Contents

- [🏥 MedyVend - Medicine Vending Machine Ecosystem](#-medyvend---medicine-vending-machine-ecosystem)
  - [📋 Table of Contents](#-table-of-contents)
  - [🎯 Project Overview](#-project-overview)
  - [🏗️ System Architecture](#️-system-architecture)
  - [💡 Key Features](#-key-features)
  - [🔧 Technology Stack](#-technology-stack)
  - [📊 Database Schema](#-database-schema)
  - [🚀 Quick Start](#-quick-start)
  - [📁 Project Structure](#-project-structure)
  - [🔌 API Documentation](#-api-documentation)
  - [⚡ Core Components](#-core-components)
  - [📈 Data Flow](#-data-flow)
  - [🔐 Security Features](#-security-features)
  - [🛠️ Configuration](#️-configuration)
  - [📱 Frontend Interface](#-frontend-interface)
  - [🔄 Background Processing](#-background-processing)
  - [📊 Monitoring & Analytics](#-monitoring--analytics)
  - [🧪 Testing](#-testing)
  - [📦 Deployment](#-deployment)
  - [🔍 Troubleshooting](#-troubleshooting)
  - [🤝 Contributing](#-contributing)
  - [📜 License](#-license)
  - [👥 Support](#-support)

---

## 🎯 Project Overview

MedyVend revolutionizes medication distribution by combining smart vending hardware with a comprehensive management system. The platform ensures secure, traceable, and efficient medicine dispensing while maintaining full compliance with pharmaceutical regulations.

### Key Benefits:
- **24/7 Automated Dispensing** - Round-the-clock medication availability
- **Inventory Management** - Real-time stock tracking and alerts
- **Audit Trail** - Complete dispensing history and compliance logging
- **Remote Monitoring** - Centralized dashboard for multiple machines
- **Integration Ready** - API-first architecture with VendTrails compatibility

---

## 🏗️ System Architecture

### High-Level Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           MedyVend Ecosystem Architecture                        │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐             │
│  │   Web Dashboard │    │   Mobile App    │    │   Admin Panel   │             │
│  │   (Laravel)     │    │  (React Native) │    │   (Laravel)     │             │
│  └─────────────────┘    └─────────────────┘    └─────────────────┘             │
│           │                       │                       │                     │
│           └───────────────────────┼───────────────────────┘                     │
│                                   │                                             │
│  ┌─────────────────────────────────┼─────────────────────────────────┐           │
│  │                    API Layer (Laravel 8.x)                      │           │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌──────────┐ │           │
│  │  │ VendTrails  │  │  Inventory  │  │  Dispensing │  │   Auth   │ │           │
│  │  │ Controller  │  │ Controller  │  │ Controller  │  │ Service  │ │           │
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └──────────┘ │           │
│  └─────────────────────────────────┼─────────────────────────────────┘           │
│                                   │                                             │
│  ┌─────────────────────────────────┼─────────────────────────────────┐           │
│  │                Message Queue Layer (RabbitMQ)                    │           │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌──────────┐ │           │
│  │  │ Dispense    │  │ Status      │  │ Inventory   │  │ Alert    │ │           │
│  │  │ Queue       │  │ Queue       │  │ Queue       │  │ Queue    │ │           │
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └──────────┘ │           │
│  └─────────────────────────────────┼─────────────────────────────────┘           │
│                                   │                                             │
│  ┌─────────────────────────────────┼─────────────────────────────────┐           │
│  │                    Database Layer (MySQL)                       │           │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌──────────┐ │           │
│  │  │ Machines    │  │ Inventory   │  │ Dispense    │  │ Audit    │ │           │
│  │  │ Table       │  │ Table       │  │ Records     │  │ Logs     │ │           │
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └──────────┘ │           │
│  └─────────────────────────────────┼─────────────────────────────────┘           │
│                                   │                                             │
│  ═══════════════════════════════════╪═══════════════════════════════════         │
│                     Physical Hardware Layer                                     │
│  ═══════════════════════════════════╪═══════════════════════════════════         │
│                                   │                                             │
│  ┌─────────────────────────────────┼─────────────────────────────────┐           │
│  │                MedyVend Hardware Platforms                       │           │
│  │                                                                   │           │
│  │  ┌─────────────────┐           ┌─────────────────┐                │           │
│  │  │   MedyVend v1   │           │   MedyVend v2   │                │           │
│  │  │ (ESP32-based)   │           │ (Raspberry Pi)  │                │           │
│  │  │   [CURRENT]     │           │ [IN DEVELOPMENT] │               │           │
│  │  └─────────────────┘           └─────────────────┘                │           │
│  │                                                                   │           │
│  │  ┌─────────────────────────────────────────────────────────────┐ │           │
│  │  │               Raspberry Pi 4 (Main Controller)              │ │           │
│  │  │  • Machine Logic & Coordination                             │ │           │
│  │  │  • Network Communication (WiFi/Ethernet)                    │ │           │
│  │  │  • RabbitMQ Client Integration                              │ │           │
│  │  │  • VendTrails API Communication                             │ │           │
│  │  │  • System Health Monitoring                                 │ │           │
│  │  │  • Security & Authentication                                │ │           │
│  │  └─────────────────────────────────────────────────────────────┘ │           │
│  │                                │                                  │           │
│  │  ┌─────────────────────────────┴─────────────────────────────────┐ │           │
│  │  │                     ESP32 Microcontroller                     │ │           │
│  │  │  • Real-time Hardware Control                                 │ │           │
│  │  │  • Sensor Data Collection                                     │ │           │
│  │  │  • Motor Control & Dispensing Logic                           │ │           │
│  │  │  • I2C/SPI Communication with Peripherals                     │ │           │
│  │  │  • GPIO Management                                            │ │           │
│  │  └─────────────────────────────┬─────────────────────────────────┘ │           │
│  │                                │                                  │           │
│  │  ┌─────────────────────────────┴─────────────────────────────────┐ │           │
│  │  │                    Hardware Components                        │ │           │
│  │  │                                                               │ │           │
│  │  │  Motors & Actuators:        │  Sensors & Feedback:           │ │           │
│  │  │  • Stepper Motors (5x)      │  • Load Cells (Weight)         │ │           │
│  │  │  • Servo Motors (3x)        │  • IR Sensors (Slot Detection) │ │           │
│  │  │  • Solenoid Locks (15x)     │  • Temperature Sensors         │ │           │
│  │  │  • Conveyor Motor           │  • Humidity Sensors            │ │           │
│  │  │  • Door Actuators           │  • Vibration Sensors           │ │           │
│  │  │                             │  • Camera Module (QR Scanner)  │ │           │
│  │  │  Power & Control:           │  • Status LEDs                 │ │           │
│  │  │  • 24V DC Power Supply      │  • Buzzer/Speaker              │ │           │
│  │  │  • Motor Driver Boards      │  • Emergency Stop Button      │ │           │
│  │  │  • Relay Modules (8ch)      │  • Touch Screen Display       │ │           │
│  │  │  • UPS Battery Backup       │  • RFID Reader                 │ │           │
│  │  └─────────────────────────────────────────────────────────────┘ │           │
│  └───────────────────────────────────────────────────────────────────┘           │
└─────────────────────────────────────────────────────────────────────────────────┘
```

*Fig 1: MedyVend Complete System Architecture*

### 🔧 Hardware Architecture Details

The MedyVend system has evolved through two distinct hardware platforms:

- **MedyVend v1**: Current production system based on ESP32 microcontroller
- **MedyVend v2**: Next-generation system in development based on Raspberry Pi 4

---

## 🚀 MedyVend v1 - ESP32-Based Architecture (Current Production)

### ⚡ **ESP32-WROOM-32D - Main Controller**
```
┌─────────────────────────────────────────────────────────────────────┐
│                      Raspberry Pi 4 (4GB RAM)                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Operating System: Raspberry Pi OS (Debian-based)                   │
│  Architecture: ARM64 (Cortex-A72 Quad-core 1.5GHz)                │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐     │
│  │   Application   │  │   Middleware    │  │   System        │     │
│  │   Layer         │  │   Layer         │  │   Services      │     │
│  │                 │  │                 │  │                 │     │
│  │ • MedyVend App  │  │ • RabbitMQ      │  │ • Systemd       │     │
│  │ • Health Monitor│  │   Client        │  │ • NetworkManager│     │
│  │ • Log Manager   │  │ • MQTT Broker   │  │ • SSH Daemon    │     │
│  │ • Update Service│  │ • Redis Cache   │  │ • Cron Jobs     │     │
│  │ • Backup Agent  │  │ • Message Queue │  │ • Log Rotation  │     │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘     │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │                    Communication Interfaces                     │ │
│  │                                                                 │ │
│  │  WiFi 802.11ac     │  Ethernet          │  USB 3.0 x4         │ │
│  │  Bluetooth 5.0     │  GPIO 40-pin       │  HDMI x2             │ │
│  │  Camera Interface  │  I2C/SPI/UART      │  Audio 3.5mm         │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

#### ⚡ **ESP32 - Real-time Hardware Controller**
```
┌─────────────────────────────────────────────────────────────────────┐
│                       ESP32-WROOM-32D                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  CPU: Dual-core Xtensa LX6 @ 240MHz                                │
│  Memory: 520KB SRAM, 4MB Flash                                     │
│  Wireless: WiFi 802.11 b/g/n, Bluetooth 4.2                       │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐     │
│  │   Motor         │  │   Sensor        │  │   Communication │     │
│  │   Control       │  │   Interface     │  │   Module        │     │
│  │                 │  │                 │  │                 │     │
│  │ • PWM Control   │  │ • ADC Readings  │  │ • WiFi Client   │     │
│  │ • Step Timing   │  │ • Digital I/O   │  │ • UART Bridge   │     │
│  │ • Direction     │  │ • Interrupt     │  │ • I2C Master    │     │
│  │   Control       │  │   Handling      │  │ • SPI Master    │     │
│  │ • Emergency     │  │ • Debouncing    │  │ • JSON Protocol │     │
│  │   Stop          │  │ • Calibration   │  │ • OTA Updates   │     │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘     │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │                      GPIO Pin Allocation                        │ │
│  │                                                                 │ │
│  │  Motors (PWM):         │  Sensors (ADC/Digital):               │ │
│  │  • GPIO 2,4,5,18,19    │  • GPIO 32,33,34,35,36 (ADC)         │ │
│  │  • GPIO 21,22,23       │  • GPIO 25,26,27 (Digital)           │ │
│  │                        │  • GPIO 12,13,14,15 (Interrupts)     │ │
│  │  Communication:        │  Control Signals:                    │ │
│  │  • GPIO 16,17 (UART)   │  • GPIO 0 (Boot/Reset)               │ │
│  │  • GPIO 6,7,8,9,10,11  │  • GPIO 1,3 (Debug UART)            │ │
│  │    (SPI Flash)         │  • GPIO 39 (Input Only)              │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

### 🔄 **Communication Architecture**

#### 📡 **RabbitMQ Message Broker Integration**
```
┌─────────────────────────────────────────────────────────────────────┐
│                        RabbitMQ Message Flow                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐ │
│  │   Laravel API   │    │   RabbitMQ      │    │  MedyVend       │ │
│  │   Backend       │    │   Broker        │    │  Machine        │ │
│  │                 │    │                 │    │                 │ │
│  │ ┌─────────────┐ │    │ ┌─────────────┐ │    │ ┌─────────────┐ │ │
│  │ │ Publisher   │─┼────┼→│ Exchange    │ │    │ │ Subscriber  │ │ │
│  │ │ Service     │ │    │ │ (Direct)    │ │    │ │ Service     │ │ │
│  │ └─────────────┘ │    │ └─────────────┘ │    │ └─────────────┘ │ │
│  │                 │    │        │        │    │        ↑        │ │
│  │ ┌─────────────┐ │    │        ↓        │    │        │        │ │
│  │ │ Consumer    │ │    │ ┌─────────────┐ │    │ ┌─────────────┐ │ │
│  │ │ Service     │←┼────┼─│ Queue       │←┼────┼─│ Publisher   │ │ │
│  │ └─────────────┘ │    │ │ (Response)  │ │    │ │ Service     │ │ │
│  └─────────────────┘    │ └─────────────┘ │    │ └─────────────┘ │ │
│                         └─────────────────┘    └─────────────────┘ │
│                                                                     │
│  Message Types:                                                     │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │                                                                 │ │
│  │  Command Messages (API → Machine):                             │ │
│  │  • dispense_medicine    • check_inventory   • system_reset      │ │
│  │  • update_config        • run_diagnostics   • emergency_stop    │ │
│  │  • calibrate_sensors    • update_firmware   • maintenance_mode  │ │
│  │                                                                 │ │
│  │  Status Messages (Machine → API):                              │ │
│  │  • dispensing_complete  • sensor_reading    • error_report      │ │
│  │  • inventory_update     • heartbeat        • maintenance_alert  │ │
│  │  • door_status         • temperature       • power_status       │ │
│  │                                                                 │ │
│  │  Queue Configuration:                                          │ │
│  │  • Exchange: medyvend.direct                                   │ │
│  │  • Routing Keys: machine.{id}.command, machine.{id}.status     │ │
│  │  • Durability: Persistent queues for reliability               │ │
│  │  • TTL: 30 minutes for commands, 24 hours for status           │ │
│  │  • Dead Letter Queue: For failed message handling              │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

### 🛠️ **Hardware Control Flow**

#### ⚙️ **Medicine Dispensing Sequence**
```
┌─────────────────────────────────────────────────────────────────────┐
│                    Medicine Dispensing Hardware Flow                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Step 1: Command Reception                                          │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  RabbitMQ → Raspberry Pi → ESP32                               │ │
│  │  Message: {"action":"dispense","slot":"A1","quantity":2}        │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                    ↓                                │
│  Step 2: Safety Checks                                             │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  • Door Lock Status    • Emergency Stop   • Power Levels       │ │
│  │  • Slot Availability   • Weight Sensors   • Temperature        │ │
│  │  • System Health       • Error States     • Calibration        │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                    ↓                                │
│  Step 3: Mechanical Sequence                                       │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Position Stepper Motor → Target Slot (Row/Column)             │ │
│  │  ├── X-Axis Motor: Move to Column Position                     │ │
│  │  ├── Y-Axis Motor: Move to Row Position                        │ │
│  │  └── Z-Axis Motor: Lower to Dispense Level                     │ │
│  │                                                                 │ │
│  │  Activate Dispensing Mechanism                                  │ │
│  │  ├── Solenoid Lock: Release Medicine Compartment               │ │
│  │  ├── Pusher Motor: Push Medicine Forward                       │ │
│  │  └── Drop Sensor: Detect Medicine Drop                         │ │
│  │                                                                 │ │
│  │  Return to Home Position                                       │ │
│  │  ├── Retract Pusher Mechanism                                  │ │
│  │  ├── Raise Z-Axis to Clear Position                            │ │
│  │  └── Return X,Y to Home (0,0)                                  │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                    ↓                                │
│  Step 4: Verification & Feedback                                   │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Weight Change Detection                                        │ │
│  │  ├── Before Weight: Load Cell Reading                          │ │
│  │  ├── After Weight: Load Cell Reading                           │ │
│  │  └── Difference: Confirm Medicine Dispensed                    │ │
│  │                                                                 │ │
│  │  Sensor Confirmations                                          │ │
│  │  ├── IR Beam Break: Medicine Passed Through                    │ │
│  │  ├── Drop Sensor: Medicine Reached Collection Area             │ │
│  │  └── Camera Verification: Visual Confirmation (Optional)       │ │
│  │                                                                 │ │
│  │  Status Reporting                                              │ │
│  │  ├── Update Inventory Count                                    │ │
│  │  ├── Log Transaction                                           │ │
│  │  └── Send Completion Message to RabbitMQ                       │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

### 📊 **Sensor & Monitoring Systems**

#### 🔍 **Real-time Monitoring Architecture**
```
┌─────────────────────────────────────────────────────────────────────┐
│                     Sensor Monitoring System                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Environmental Monitoring:                                          │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Temperature & Humidity (DHT22)                                │ │
│  │  ├── Medicine Storage: 15-25°C, <60% RH                        │ │
│  │  ├── Electronics Compartment: <40°C                            │ │
│  │  └── Alerts: Over-temperature, High Humidity                   │ │
│  │                                                                 │ │
│  │  Power Monitoring (INA219)                                     │ │
│  │  ├── Main Power: 24V DC Supply Monitoring                      │ │
│  │  ├── Battery Backup: UPS Status & Charge Level                 │ │
│  │  └── Power Consumption: Per-component Usage                    │ │
│  │                                                                 │ │
│  │  Vibration Detection (MPU6050)                                 │ │
│  │  ├── Tampering Detection: Unusual Movement                     │ │
│  │  ├── Transport Monitoring: Excessive Vibration                 │ │
│  │  └── Maintenance Alerts: Component Wear                        │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Inventory Monitoring:                                             │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Weight Sensors (HX711 + Load Cells)                           │ │
│  │  ├── Per-Slot Weight Monitoring: 0.1g Precision                │ │
│  │  ├── Medicine Count Calculation: Weight-based Counting         │ │
│  │  └── Theft Detection: Unexpected Weight Changes                │ │
│  │                                                                 │ │
│  │  IR Slot Sensors (Photoelectric)                              │ │
│  │  ├── Slot Occupancy: Beam Break Detection                      │ │
│  │  ├── Medicine Dispensing: Drop Confirmation                    │ │
│  │  └── Jam Detection: Stuck Medicine Alert                       │ │
│  │                                                                 │ │
│  │  Camera System (Pi Camera V2)                                  │ │
│  │  ├── QR Code Scanning: Patient/Medicine Verification           │ │
│  │  ├── Visual Inventory: Stock Level Confirmation                │ │
│  │  └── Security Recording: Dispensing Event Capture              │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Security & Access Control:                                        │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Door Sensors (Reed Switch/Magnetic)                           │ │
│  │  ├── Main Access Door: Locked/Unlocked Status                  │ │
│  │  ├── Medicine Compartment: Individual Slot Doors               │ │
│  │  └── Service Panel: Maintenance Access                         │ │
│  │                                                                 │ │
│  │  RFID Access Control (RC522)                                   │ │
│  │  ├── Staff Authentication: Maintenance Access                  │ │
│  │  ├── Patient Identification: Prescription Verification         │ │
│  │  └── Audit Trail: Access Logging                               │ │
│  │                                                                 │ │
│  │  Emergency Systems                                             │ │
│  │  ├── Emergency Stop Button: Immediate System Halt              │ │
│  │  ├── Fire Sensor: Smoke/Heat Detection                        │ │
│  │  └── Panic Button: Silent Alarm Activation                    │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

### 🔧 **Software Stack & Communication Protocols**

#### 💻 **Embedded Software Architecture**
```
┌─────────────────────────────────────────────────────────────────────┐
│                    Raspberry Pi Software Stack                      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Operating System Layer:                                            │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Raspberry Pi OS (Debian 11 Bullseye)                          │ │
│  │  ├── Kernel: Linux 5.15.x ARM64                                │ │
│  │  ├── Init System: systemd                                      │ │
│  │  ├── Package Manager: apt                                      │ │
│  │  └── Security: UFW Firewall, SSH Key Auth                      │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Application Layer (Python 3.9+):                                  │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  MedyVend Control Application                                   │ │
│  │  ├── Flask Web Server: Local API & Dashboard                   │ │
│  │  ├── RabbitMQ Client: pika library                             │ │
│  │  ├── Database: SQLite for local storage                        │ │
│  │  ├── GPIO Control: RPi.GPIO library                            │ │
│  │  ├── Serial Communication: PySerial for ESP32                  │ │
│  │  ├── Camera Interface: picamera library                        │ │
│  │  ├── Scheduling: APScheduler for tasks                         │ │
│  │  └── Logging: structured JSON logs                             │ │
│  │                                                                 │ │
│  │  Key Modules:                                                  │ │
│  │  ├── machine_controller.py: Main control logic                │ │
│  │  ├── rabbitmq_handler.py: Message queue interface             │ │
│  │  ├── esp32_interface.py: Hardware communication               │ │
│  │  ├── sensor_monitor.py: Sensor data collection                │ │
│  │  ├── inventory_manager.py: Stock tracking                     │ │
│  │  ├── security_module.py: Access control & auth               │ │
│  │  └── health_monitor.py: System health checks                  │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Service Layer:                                                     │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Systemd Services:                                             │ │
│  │  ├── medyvend-controller.service: Main application            │ │
│  │  ├── medyvend-monitor.service: Health monitoring              │ │
│  │  ├── medyvend-backup.service: Data backup                     │ │
│  │  └── medyvend-update.service: OTA updates                     │ │
│  │                                                                 │ │
│  │  Cron Jobs:                                                    │ │
│  │  ├── Health Reports: Every 5 minutes                          │ │
│  │  ├── Inventory Sync: Every hour                               │ │
│  │  ├── Log Rotation: Daily                                      │ │
│  │  └── System Backup: Weekly                                    │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                      ESP32 Firmware Stack                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Development Framework:                                             │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  ESP-IDF (Espressif IoT Development Framework) v4.4             │ │
│  │  ├── FreeRTOS: Real-time operating system                      │ │
│  │  ├── ESP32 HAL: Hardware abstraction layer                     │ │
│  │  ├── WiFi Stack: 802.11 b/g/n support                         │ │
│  │  ├── Bluetooth: Classic + BLE support                          │ │
│  │  └── OTA Updates: Over-the-air firmware updates                │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Application Tasks (FreeRTOS):                                     │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Core Tasks:                                                   │ │
│  │  ├── main_task: Primary control loop                          │ │
│  │  ├── comm_task: UART communication with Pi                    │ │
│  │  ├── motor_task: Stepper/servo motor control                  │ │
│  │  ├── sensor_task: ADC readings & sensor monitoring            │ │
│  │  ├── safety_task: Emergency stop & safety systems             │ │
│  │  └── watchdog_task: System health monitoring                  │ │
│  │                                                                 │ │
│  │  Task Priorities:                                              │ │
│  │  ├── safety_task: Priority 5 (Highest)                        │ │
│  │  ├── motor_task: Priority 4                                   │ │
│  │  ├── sensor_task: Priority 3                                  │ │
│  │  ├── comm_task: Priority 2                                    │ │
│  │  └── main_task: Priority 1                                    │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Hardware Abstraction:                                             │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Driver Modules:                                               │ │
│  │  ├── stepper_driver.c: Stepper motor control                  │ │
│  │  ├── servo_driver.c: Servo motor PWM control                  │ │
│  │  ├── sensor_driver.c: ADC & digital sensor interface          │ │
│  │  ├── safety_driver.c: Emergency & safety systems              │ │
│  │  └── comm_driver.c: UART protocol implementation              │ │
│  │                                                                 │ │
│  │  Configuration:                                               │ │
│  │  ├── motor_config.h: Motor parameters & timing                │ │
│  │  ├── sensor_config.h: Sensor calibration data                 │ │
│  │  ├── pin_config.h: GPIO pin assignments                       │ │
│  │  └── system_config.h: System-wide settings                    │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

### 📡 **Network & Communication Protocols**

```
┌─────────────────────────────────────────────────────────────────────┐
│                    Communication Protocol Stack                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Internet Layer:                                                    │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Primary: WiFi 802.11n (2.4/5GHz)                              │ │
│  │  ├── SSID: MedyVend_Network                                     │ │
│  │  ├── Security: WPA2-Enterprise                                 │ │
│  │  ├── IP Assignment: DHCP with reservation                      │ │
│  │  └── Fallback: Mobile Hotspot (4G/5G)                          │ │
│  │                                                                 │ │
│  │  Backup: Ethernet (Wired)                                      │ │
│  │  ├── Connection: CAT6 cable                                    │ │
│  │  ├── Protocol: IEEE 802.3                                     │ │
│  │  └── PoE Support: Power over Ethernet capability               │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Application Protocols:                                            │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  RabbitMQ/AMQP 0.9.1:                                          │ │
│  │  ├── Server: medyvend-broker.example.com:5672                  │ │
│  │  ├── Authentication: Username/Password + SSL/TLS               │ │
│  │  ├── Virtual Host: /medyvend                                   │ │
│  │  ├── Exchange: medyvend.direct (durable)                       │ │
│  │  └── Queues: machine.{id}.cmd, machine.{id}.status             │ │
│  │                                                                 │ │
│  │  HTTP/HTTPS (REST API):                                        │ │
│  │  ├── VendTrails API: https://emapi.vendtrails.com              │ │
│  │  ├── Laravel Backend: https://api.medyvend.com                 │ │
│  │  ├── Authentication: JWT tokens                                │ │
│  │  └── Rate Limiting: 100 requests/minute                        │ │
│  │                                                                 │ │
│  │  MQTT (Alternative):                                           │ │
│  │  ├── Broker: medyvend-mqtt.example.com:8883                    │ │
│  │  ├── Topics: medyvend/{id}/cmd, medyvend/{id}/status           │ │
│  │  ├── QoS Level: 1 (At least once delivery)                    │ │
│  │  └── Retain: Status messages retained                          │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Inter-Component Communication:                                     │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │  Raspberry Pi ←→ ESP32:                                         │ │
│  │  ├── Physical: UART (3.3V TTL)                                 │ │
│  │  ├── Baud Rate: 115200 bps                                     │ │
│  │  ├── Protocol: Custom JSON-based                               │ │
│  │  ├── Flow Control: RTS/CTS hardware                            │ │
│  │  └── Error Handling: CRC checksum validation                   │ │
│  │                                                                 │ │
│  │  ESP32 ←→ Sensors/Motors:                                       │ │
│  │  ├── I2C Bus: Temperature, humidity, IMU sensors               │ │
│  │  ├── SPI Bus: High-speed ADCs, display modules                 │ │
│  │  ├── PWM Signals: Motor control (20kHz frequency)              │ │
│  │  ├── GPIO: Digital sensors, switches, LEDs                     │ │
│  │  └── ADC: Analog sensors, voltage monitoring                   │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

This comprehensive hardware architecture section provides complete technical details about:

1. **🖥️ Raspberry Pi Controller** - Main system coordination and network communication
2. **⚡ ESP32 Microcontroller** - Real-time hardware control and sensor management  
3. **📡 RabbitMQ Integration** - Message queuing for reliable communication
4. **🔧 Hardware Components** - Motors, sensors, and control systems
5. **⚙️ Control Flow** - Step-by-step dispensing sequence
6. **📊 Monitoring Systems** - Environmental and security sensors
7. **💻 Software Stack** - Complete embedded software architecture
8. **📡 Communication Protocols** - Network and inter-component communication

The architecture shows how the ESP32 and Raspberry Pi work together with RabbitMQ to create a robust, real-time medicine dispensing system with comprehensive monitoring and fail-safe mechanisms.

---

## 💡 Key Features

### 🏥 Healthcare-Focused
- **Prescription Integration** - Links with doctor prescriptions and patient records
- **Expiry Management** - Automatic tracking and alerts for medication expiry
- **Batch Tracking** - Complete traceability from manufacturing to dispensing
- **Compliance Reporting** - Audit trails for regulatory compliance

### 🤖 Smart Automation
- **AI-Powered Inventory** - Predictive restocking based on usage patterns
- **Real-time Monitoring** - Live machine status and health monitoring
- **Automatic Dispensing** - Queue-based processing with retry logic
- **Error Recovery** - Automatic fault detection and recovery procedures

### 📊 Analytics & Reporting
- **Usage Analytics** - Detailed reporting on dispensing patterns
- **Stock Optimization** - Data-driven inventory management
- **Performance Metrics** - Machine uptime and efficiency tracking
- **Financial Reports** - Revenue and transaction analysis

### 🔒 Security & Compliance
- **Multi-level Authentication** - Role-based access control
- **Encrypted Communication** - Secure API communications
- **Audit Logging** - Comprehensive activity tracking
- **Data Privacy** - GDPR/HIPAA compliant data handling

---

## 🔧 Technology Stack

### Backend
- **Framework**: Laravel 8.x
- **Language**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Queue System**: Redis/Database
- **Caching**: Redis
- **API**: RESTful APIs with Sanctum authentication

### Frontend
- **Template Engine**: Laravel Blade
- **CSS Framework**: Bootstrap 5
- **JavaScript**: Vanilla JS + jQuery
- **Charts**: Chart.js
- **Icons**: FontAwesome 6

### DevOps & Infrastructure
- **Web Server**: Apache/Nginx
- **OS**: Ubuntu 22.04 LTS
- **Process Manager**: Supervisor
- **Monitoring**: Laravel Telescope
- **Logging**: Custom log channels

### Integrations
- **VendTrails API**: Hardware control and monitoring
- **Payment Gateways**: Multiple payment provider support
- **SMS/Email**: Notification services
- **ABDM Integration**: Healthcare record integration (future)

---

## 📊 Database Schema

```
[Database Schema Diagram Placeholder]
```

*Fig 3: Database Entity Relationship Diagram*

### Core Tables:

| Table | Purpose | Key Relationships |
|-------|---------|------------------|
| `vending_machine` | Machine registry and configuration | → `vending_machine_inventory` |
| `drugs` | Master drug catalog | → `vending_machine_inventory` |
| `vending_machine_inventory` | Stock levels and slot mapping | `vending_machine` ← → `drugs` |
| `vending_dispense_records` | Dispensing transaction log | → `vending_machine`, `drugs` |
| `users` | System users and authentication | → `vending_dispense_records` |

### Sample Schema:

```sql
-- Vending Machine Configuration
CREATE TABLE vending_machine (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    machine_id VARCHAR(50) UNIQUE,
    machine_num INT UNIQUE,
    machine_name VARCHAR(255),
    machine_lat DECIMAL(10,7),
    machine_long DECIMAL(10,7),
    machine_is_active BOOLEAN DEFAULT TRUE,
    machine_last_ping TIMESTAMP,
    vendtrails_access_token TEXT,
    vendtrails_refresh_token TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Drug Master Data
CREATE TABLE drugs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    generic_name VARCHAR(255),
    manufacturer VARCHAR(255),
    category VARCHAR(100),
    strength VARCHAR(50),
    price DECIMAL(10,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Inventory Management
CREATE TABLE vending_machine_inventory (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    vending_machine_id BIGINT,
    drug_id BIGINT,
    slot_row INT,
    slot_column INT,
    stock_quantity INT DEFAULT 0,
    threshold_quantity INT,
    expiry_date DATE,
    batch_number VARCHAR(100),
    last_restocked_at TIMESTAMP,
    FOREIGN KEY (vending_machine_id) REFERENCES vending_machine(id),
    FOREIGN KEY (drug_id) REFERENCES drugs(id),
    UNIQUE KEY unique_machine_slot (vending_machine_id, slot_row, slot_column)
);
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- Composer
- MySQL 8.0+
- Node.js & NPM (for asset compilation)
- Redis (recommended for queues)

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-org/medyvend-poc.git
   cd medyvend-poc
   ```

2. **Install Dependencies**
   ```bash
   # Backend dependencies
   composer install
   
   # Frontend dependencies (if using Laravel Mix)
   npm install
   ```

3. **Environment Setup**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   
   # Configure database in .env file
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=vending_poc
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE vending_poc;"
   
   # Run migrations
   php artisan migrate
   
   # Seed sample data
   php artisan db:seed --class=VendingSystemSeeder
   ```

5. **Queue Configuration**
   ```bash
   # For production, use Redis
   QUEUE_CONNECTION=redis
   
   # For development, use database
   QUEUE_CONNECTION=database
   php artisan queue:table
   php artisan migrate
   ```

6. **Start the Application**
   ```bash
   # Development server
   php artisan serve
   
   # Queue worker (separate terminal)
   php artisan queue:work
   
   # Schedule runner (for cron jobs)
   php artisan schedule:run
   ```

### First Login
- **URL**: `http://localhost:8000/admin/dashboard`
- **Default Admin**: admin@medyvend.com / password123

---

## 📁 Project Structure

```
medyvend-poc/
├── 📁 app/
│   ├── 📁 Http/Controllers/
│   │   ├── VendingMachineController.php     # Machine CRUD & operations
│   │   └── VendingInventoryController.php   # Inventory management
│   ├── 📁 Models/
│   │   ├── VendingMachine.php               # Machine model
│   │   ├── VendingMachineInventory.php      # Inventory model
│   │   ├── VendingDispenseRecord.php        # Transaction model
│   │   └── Drug.php                         # Drug master model
│   ├── 📁 Services/
│   │   └── VendTrailsService.php            # VendTrails API integration
│   ├── 📁 Jobs/
│   │   ├── DispatchMedsToVendtrails.php     # Async dispensing job
│   │   └── CheckVendtrailsTaskStatus.php    # Status monitoring job
│   └── 📁 Providers/
│       └── VendingServiceProvider.php       # Service bindings
├── 📁 resources/views/
│   ├── 📁 layouts/
│   │   └── admin.blade.php                  # Admin layout template
│   └── 📁 admin/
│       ├── dashboard.blade.php              # Main dashboard
│       ├── 📁 machines/
│       │   └── index.blade.php              # Machine management
│       └── 📁 inventory/
│           └── index.blade.php              # Inventory management
├── 📁 database/
│   ├── 📁 migrations/                       # Database schema
│   └── 📁 seeders/
│       └── VendingSystemSeeder.php          # Sample data
├── 📁 routes/
│   ├── web.php                              # Web interface routes
│   └── api.php                              # API routes (v1 & v2)
├── 📁 config/
│   └── logging.php                          # Custom log channels
└── 📁 storage/logs/
    └── vending_trails.log                   # VendTrails specific logs
```

---

## 🔌 API Documentation

### Base URLs
- **V1 API**: `/api/v1/vending/`
- **V2 API**: `/api/v2/vending/`

### Authentication
```http
Authorization: Bearer {your-api-token}
Content-Type: application/json
```

### Core Endpoints

#### Machine Operations
```http
POST /api/v2/vending/generate-token
POST /api/v2/vending/machine-details
POST /api/v2/vending/send-instruction
POST /api/v2/vending/check-status
POST /api/v2/vending/hard-reset
```

#### Inventory Management
```http
GET    /api/v2/vending/inventory           # List inventory
POST   /api/v2/vending/inventory           # Add inventory item
GET    /api/v2/vending/inventory/{id}      # Get specific item
PUT    /api/v2/vending/inventory/{id}      # Update inventory
DELETE /api/v2/vending/inventory/{id}      # Remove item
```

#### Medicine Dispensing
```http
GET  /api/v2/vending/get/meds             # Get available medicines
POST /api/v2/vending/dispense-meds        # Dispense medicines
```

### Sample API Calls

#### Dispense Medicine
```bash
curl -X POST "http://localhost:8000/api/v2/vending/dispense-meds" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your-token" \
  -d '{
    "prescription_id": 12345,
    "invoice_id": 67890,
    "selected_tablets": [
      {"drug_id": 1, "quantity": 2},
      {"drug_id": 3, "quantity": 1}
    ],
    "transaction_ref": "TXN123456789",
    "machine_id": 1
  }'
```

#### Check Machine Status
```bash
curl -X POST "http://localhost:8000/api/v2/vending/check-status" \
  -H "Content-Type: application/json" \
  -d '{
    "task_num": 12345,
    "machine_num": 1
  }'
```

---

## ⚡ Core Components

### VendingMachineController
Handles all machine-related operations including:
- Machine registration and configuration
- Token generation and refresh
- Hardware communication via VendTrails
- Machine status monitoring
- Hard reset operations

### VendingInventoryController
Manages inventory operations:
- Stock level tracking
- Low stock alerts
- Expiry date monitoring
- Batch number tracking
- CRUD operations for inventory items

### VendTrailsService
Core integration service for hardware communication:
- Secure API communication
- Automatic token refresh
- Error handling and retry logic
- Task status monitoring
- Machine instruction dispatch

### Background Jobs
Asynchronous processing for:
- Medicine dispensing operations
- Status monitoring and polling
- Inventory updates
- Alert notifications
- Report generation

---

## 📈 Data Flow

### Medicine Dispensing Workflow

```
[Dispensing Flow Diagram Placeholder]
```

*Fig 4: Medicine Dispensing Process Flow*

1. **Prescription Creation** - Doctor creates prescription for patient
2. **Invoice Generation** - System generates billing invoice
3. **Dispensing Request** - Medicine dispensing request initiated
4. **Slot Mapping** - System maps drugs to vending machine slots
5. **Queue Processing** - Background job processes dispensing request
6. **Hardware Communication** - API call sent to VendTrails service
7. **Machine Operation** - Physical dispensing from machine
8. **Status Monitoring** - System polls for completion status
9. **Transaction Recording** - Success/failure recorded in database
10. **Inventory Update** - Stock levels automatically updated
11. **Notification** - Patient/staff notified of completion

### Inventory Management Flow

```
[Inventory Flow Diagram Placeholder]
```

*Fig 5: Inventory Management Process*

1. **Stock Monitoring** - Continuous tracking of inventory levels
2. **Threshold Alerts** - Automatic low stock notifications
3. **Restocking Process** - Manual or automated restocking
4. **Expiry Tracking** - Monitor medication expiry dates
5. **Batch Management** - Track batch numbers for recalls
6. **Audit Trail** - Complete history of inventory changes

---

## 🔐 Security Features

### Authentication & Authorization
- **Multi-factor Authentication** - Enhanced security for admin access
- **Role-based Access Control** - Granular permissions system
- **API Token Management** - Secure token generation and rotation
- **Session Management** - Secure session handling

### Data Protection
- **Encryption at Rest** - Database encryption for sensitive data
- **Encryption in Transit** - TLS/SSL for all communications
- **API Security** - Rate limiting and request validation
- **Audit Logging** - Comprehensive activity tracking

### Compliance
- **GDPR Compliance** - Data privacy and protection
- **HIPAA Ready** - Healthcare data security standards
- **FDA Compliance** - Pharmaceutical traceability requirements
- **Local Regulations** - Adaptable to regional requirements

---

## 🛠️ Configuration

### Environment Variables
```bash
# Application
APP_NAME="MedyVend"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vending_poc
DB_USERNAME=vending_user
DB_PASSWORD=secure_password

# Queue Configuration
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# VendTrails Integration
VENDTRAILS_API_URL=https://emapi.vendtrails.com/api
VENDTRAILS_COMPANY_NUM=2

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info
```

---

## 📱 Frontend Interface

### Dashboard Features
- **Real-time Statistics** - Live machine and inventory stats
- **Machine Status Grid** - Visual status of all machines
- **Dispensing Activity** - Recent transactions and trends
- **Alert Center** - Critical notifications and warnings
- **Analytics Charts** - Usage patterns and performance metrics

### Machine Management
- **Machine Registry** - Add, edit, and configure machines
- **Status Monitoring** - Real-time machine health
- **Remote Operations** - Test connections and reset machines
- **Configuration** - Update machine settings and parameters

### Inventory Control
- **Stock Overview** - Current inventory levels across machines
- **Restocking Interface** - Easy stock management tools
- **Expiry Alerts** - Medication expiry monitoring
- **Batch Tracking** - Complete batch traceability
- **Reports** - Comprehensive inventory reports

### User Interface Screenshots
```
[Dashboard Screenshot Placeholder]
[Machine Management Screenshot Placeholder]
[Inventory Management Screenshot Placeholder]
```

---

## 🔄 Background Processing

### Job Queue System
The system uses Laravel's queue system for asynchronous processing:

#### DispatchMedsToVendtrails Job
- **Purpose**: Handle medicine dispensing requests
- **Processing**: Communicates with VendTrails API
- **Retry Logic**: 3 attempts with exponential backoff
- **Error Handling**: Comprehensive error logging and alerting

#### CheckVendtrailsTaskStatus Job
- **Purpose**: Monitor dispensing task completion
- **Polling**: Automatic status checking every 30 seconds
- **Completion**: Updates records and inventory on success
- **Timeout**: Handles failed or stuck tasks

### Queue Workers
```bash
# Start queue workers
php artisan queue:work --queue=vending --tries=3 --timeout=300

# Monitor queue status
php artisan queue:monitor vending

# Clear failed jobs
php artisan queue:flush
```

---

## 📊 Monitoring & Analytics

### Performance Monitoring
- **Response Times** - API and web interface performance
- **Queue Processing** - Job completion times and failure rates
- **Database Performance** - Query optimization and indexing
- **Machine Uptime** - Hardware availability tracking

### Business Analytics
- **Usage Patterns** - Peak hours and demand forecasting
- **Inventory Turnover** - Stock rotation and optimization
- **Revenue Analytics** - Financial performance tracking
- **Patient Satisfaction** - Service quality metrics

---

## 🧪 Testing

### Test Structure
```
tests/
├── Feature/
│   ├── VendingMachineTest.php
│   ├── InventoryManagementTest.php
│   └── DispensingWorkflowTest.php
├── Unit/
│   ├── VendTrailsServiceTest.php
│   ├── ModelTest.php
│   └── JobTest.php
└── Integration/
    └── VendTrailsIntegrationTest.php
```

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

---

## 📦 Deployment

### Production Environment Setup

#### Server Requirements
- **OS**: Ubuntu 22.04 LTS or CentOS 8
- **Web Server**: Nginx or Apache 2.4+
- **PHP**: 7.4+ with extensions
- **Database**: MySQL 8.0+ or MariaDB 10.4+
- **Cache**: Redis 6.0+
- **Process Manager**: Supervisor for queue workers

#### Deployment Script
```bash
#!/bin/bash
# deploy.sh - Production deployment script

# Pull latest code
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Restart queue workers
sudo supervisorctl restart laravel-worker:*

# Reload web server
sudo systemctl reload nginx
```

---

## 🔍 Troubleshooting

### Common Issues

#### Queue Jobs Not Processing
```bash
# Check queue status
php artisan queue:monitor

# Restart queue workers
php artisan queue:restart

# Check failed jobs
php artisan queue:failed
```

#### VendTrails API Connection Issues
```bash
# Check VendTrails service logs
tail -f storage/logs/vending_trails.log

# Test API connectivity
curl -X POST "https://emapi.vendtrails.com/api/generate_token" \
  -d "company_num=2"
```

---

## 🤝 Contributing

We welcome contributions to MedyVend! Please follow these guidelines:

### Development Workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass (`php artisan test`)
6. Commit your changes (`git commit -am 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Create a Pull Request

---

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👥 Support

### Community
- **GitHub Discussions**: [GitHub Issues](https://github.com/medyvend/discussions)
- **Documentation**: Complete API and user documentation
- **Video Tutorials**: Step-by-step implementation guides

### Professional Support
- **Technical Support**: support@medyvend.com
- **Sales Inquiries**: sales@medyvend.com
- **Partnership**: partners@medyvend.com

---

**Made with ❤️ by the MedyVend Team**

*Revolutionizing healthcare through intelligent automation*

---

*This README was last updated on: December 2024*
*For the most current information, please visit our documentation site*
