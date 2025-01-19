#!/bin/bash
# Aguardar até o RabbitMQ estar totalmente inicializado
sleep 10

# Criar a fila "send_email_sales"
rabbitmqadmin -u user -p password declare queue name=send_email_sales durable=true
