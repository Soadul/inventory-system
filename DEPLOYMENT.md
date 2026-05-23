# AWS EC2 Deployment Guide - OS-Inventory Management Software

This guide outlines the step-by-step procedures to deploy your **Inventory Management Software** to an AWS EC2 instance. Since the application is already **Docker-ready**, the deployment process is extremely straightforward and secure.

---

## 🛠️ Step 1: Launch an AWS EC2 Instance

1. **Log in** to your [AWS Management Console](https://aws.amazon.com/).
2. Navigate to **EC2 Dashboard** and click **Launch Instance**.
3. **Name your instance**: e.g., `OS-Inventory-Server`.
4. **Choose an Operating System (AMI)**: Select **Ubuntu** (Ubuntu Server 24.04 LTS or 22.04 LTS).
5. **Select Instance Type**: Choose **`t2.micro`** or **`t3.micro`** (Free Tier eligible!).
6. **Select / Create Key Pair (SSH)**:
   - Create a new key pair (e.g., `inventory-key.pem`), download it, and keep it safe!
7. **Configure Network / Security Group (Crucial!)**:
   - Check **Allow SSH traffic from** (limit to your IP for maximum security, or select anywhere).
   - Check **Allow HTTP traffic from the internet** (this automatically opens **port 80**).
8. Click **Launch Instance** in the bottom right corner.

---

## 🔒 Step 2: Configure EC2 Security Group for HTTP

By default, our local Docker Compose runs on port `8080`. In a production EC2 server, we want visitors to access the website by entering just the server's public IP address (without typing `:8080`).

We will accomplish this by mapping the container's internal port `80` directly to the host's public port `80`.

Ensure your **Inbound Rules** in your EC2 Security Group look like this:

| Type | Protocol | Port Range | Source | Description |
| :--- | :--- | :--- | :--- | :--- |
| **SSH** | TCP | `22` | `My IP` or `0.0.0.0/0` | Secure Terminal Access |
| **HTTP** | TCP | `80` | `0.0.0.0/0` | Public Web Traffic |

---

## 📂 Step 3: Transfer Code to EC2 Server

You can move your code from your local machine to your EC2 instance using **Git** (recommended) or **SCP**.

### Method A: Using Git (Easiest)
1. Initialize a Git repository on your local desktop, commit the files, and push to a private/public GitHub repository.
2. Connect to your EC2 instance via SSH (see Step 4) and clone it:
   ```bash
   git clone <YOUR_GITHUB_REPOSITORY_URL>
   ```

### Method B: Using SCP (Direct File Transfer)
Run this command from your **local machine's terminal** (where your `.pem` key and code are stored):
```bash
# Set secure permission on the private key file
chmod 400 inventory-key.pem

# Compress and upload the project directory
tar -czf inventory.tar.gz -C /home/soadulislam/Desktop/inventory-system .
scp -i inventory-key.pem inventory.tar.gz ubuntu@<YOUR_EC2_PUBLIC_IP>:/home/ubuntu/
```

---

## 💻 Step 4: Access your EC2 Server & Install Docker

1. **Connect via SSH** from your local terminal:
   ```bash
   ssh -i "inventory-key.pem" ubuntu@<YOUR_EC2_PUBLIC_IP>
   ```
2. If you uploaded a compressed `tar.gz` archive (Method B), extract it:
   ```bash
   mkdir inventory-system
   tar -xzf inventory.tar.gz -C inventory-system
   cd inventory-system
   ```
3. **Install Docker & Docker Compose** in 1 click using this standard script:
   ```bash
   # Update package database
   sudo apt update && sudo apt upgrade -y

   # Install Docker
   sudo apt install -y docker.io docker-compose-v2

   # Enable Docker service
   sudo systemctl enable --now docker

   # Add current ubuntu user to docker group to run without sudo
   sudo usermod -aG docker $USER
   
   # IMPORTANT: Run this to apply group changes immediately
   newgrp docker
   ```

---

## ⚙️ Step 5: Configure Port Mapping for Production (Port 80)

To map the application to standard HTTP Port 80 instead of 8080:
1. Open `docker-compose.yml` on the EC2 server:
   ```bash
   nano docker-compose.yml
   ```
2. Modify the `ports` mapping to bind **host port `80`** to container port `80`:
   ```yaml
   ports:
     - "80:80"
   ```
3. Press `CTRL+O` and `Enter` to save, and `CTRL+X` to exit nano.

---

## 🚀 Step 6: Start Container & Configure Permissions

1. Set the SQLite folder write permissions to prevent database errors (just as you did locally):
   ```bash
   chmod -R 777 app/database
   ```
2. **Start the application** in detached mode:
   ```bash
   docker compose up -d --build
   ```
3. Verify the container is running:
   ```bash
   docker compose ps
   ```

---

## 🎉 Step 7: Access Your Live Application!

Copy your **EC2 Instance Public IP Address** (e.g., `54.210.12.85`) from your AWS Console and paste it directly into your web browser:

👉 **`http://<YOUR_EC2_PUBLIC_IP>`**

Your custom **OS-Inventory Management System** is now globally hosted and operational! You can securely log in using your pre-seeded admin credentials from anywhere in the world.
