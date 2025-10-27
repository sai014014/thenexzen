#!/bin/bash

# Redis Security Setup Script for Nexzen Application
# Run this script on your production server after installation

echo "🔒 Starting Redis Security Hardening..."

# 1. Set Redis Password
echo "📝 Setting Redis password..."
read -sp "Enter a strong password for Redis: " REDIS_PASS
echo ""

# 2. Update Redis Configuration
echo "⚙️ Updating Redis configuration..."
sudo sed -i "s/# requirepass.*/requirepass $REDIS_PASS/" /etc/redis/redis.conf
sudo sed -i "s/bind.*/# bind 127.0.0.1/" /etc/redis/redis.conf
echo "bind 127.0.0.1" | sudo tee -a /etc/redis/redis.conf

# 3. Enable Protected Mode
echo "🛡️ Enabling protected mode..."
sudo sed -i "s/protected-mode.*/protected-mode yes/" /etc/redis/redis.conf

# 4. Disable Dangerous Commands
echo "🚫 Disabling dangerous commands..."
echo "rename-command FLUSHDB \"\"" | sudo tee -a /etc/redis/redis.conf
echo "rename-command FLUSHALL \"\"" | sudo tee -a /etc/redis/redis.conf
echo "rename-command CONFIG \"\"" | sudo tee -a /etc/redis/redis.conf

# 5. Set Memory Limits
echo "💾 Setting memory limits..."
sudo sed -i "s/# maxmemory.*/maxmemory 256mb/" /etc/redis/redis.conf
sudo sed -i "s/# maxmemory-policy.*/maxmemory-policy allkeys-lru/" /etc/redis/redis.conf

# 6. Enable AOF Persistence
echo "💿 Enabling AOF persistence..."
sudo sed -i "s/appendonly.*/appendonly yes/" /etc/redis/redis.conf

# 7. Restart Redis
echo "🔄 Restarting Redis..."
sudo systemctl restart redis
sudo systemctl enable redis

# 8. Configure Firewall
echo "🔥 Configuring firewall..."
if command -v ufw &> /dev/null; then
    sudo ufw deny 6379
    sudo ufw reload
    echo "✅ UFW firewall configured"
elif command -v firewall-cmd &> /dev/null; then
    sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" port protocol="tcp" port="6379" reject'
    sudo firewall-cmd --reload
    echo "✅ Firewalld configured"
fi

# 9. Verify Configuration
echo "🧪 Testing Redis connection..."
if redis-cli -a "$REDIS_PASS" ping | grep -q "PONG"; then
    echo "✅ Redis is running and secured"
else
    echo "❌ Redis connection failed. Check your configuration."
    exit 1
fi

# 10. Display Summary
echo ""
echo "✅ Redis Security Hardening Complete!"
echo ""
echo "📋 Next Steps:"
echo "1. Update your .env file with: REDIS_PASSWORD=$REDIS_PASS"
echo "2. Run: php artisan config:clear && php artisan config:cache"
echo "3. Test the application"
echo "4. Monitor Redis: redis-cli -a $REDIS_PASS INFO"
echo ""
echo "🔐 Important: Save the Redis password securely!"
echo "Password: $REDIS_PASS"

