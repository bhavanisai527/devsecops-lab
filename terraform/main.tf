terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  region = var.aws_region
}

# ECR stores my Docker images
# my pipeline builds and pushes images here
# my EC2 pulls images from here to run the app
resource "aws_ecr_repository" "laravel_app" {
  name                 = "laravel-lab"
  image_tag_mutability = "MUTABLE"

  image_scanning_configuration {
    scan_on_push = true
  }

  tags = {
    Name        = "laravel-lab-ecr"
    Environment = var.environment
  }
}

# IAM role gives my EC2 permission to pull images from ECR
resource "aws_iam_role" "ec2_role" {
  name = "devsecops-ec2-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Action    = "sts:AssumeRole"
      Effect    = "Allow"
      Principal = { Service = "ec2.amazonaws.com" }
    }]
  })
}

# attaching ECR read only policy to my role
# EC2 can pull images but cannot push or delete

resource "aws_iam_role_policy_attachment" "ecr_policy" {
  role       = aws_iam_role.ec2_role.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonEC2ContainerRegistryReadOnly"
}

# EC2 needs a profile wrapper to use the IAM role

resource "aws_iam_instance_profile" "ec2_profile" {
  name = "devsecops-ec2-profile"
  role = aws_iam_role.ec2_role.name
}

# security group controls traffic to my EC2
# port 8000 open so users can visit my Laravel app
# all outbound allowed so EC2 can pull images and updates

resource "aws_security_group" "devsecops_sg" {
  name        = "devsecops-lab-sg"
  description = "Security group for DevSecOps lab EC2"

  ingress {
    description = "Laravel app"
    from_port   = 8000
    to_port     = 8000
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    description = "allow all outbound"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "devsecops-lab-sg"
  }
}

# EC2 instance runs my Laravel Docker container
# t2.micro is free tier eligible
# user_data runs on first boot to install Docker and start my app

resource "aws_instance" "devsecops_lab" {
  ami                  = "ami-0c02fb55956c7d316"
  instance_type        = var.instance_type
  security_groups      = [aws_security_group.devsecops_sg.name]
  iam_instance_profile = aws_iam_instance_profile.ec2_profile.name

  user_data = <<-EOF
    #!/bin/bash
    yum update -y
    yum install -y docker
    systemctl start docker
    systemctl enable docker

    aws ecr get-login-password --region ${var.aws_region} | \
    docker login --username AWS --password-stdin \
    $(aws sts get-caller-identity --query Account --output text).dkr.ecr.${var.aws_region}.amazonaws.com

    ECR_URI=$(aws ecr describe-repositories \
      --repository-names laravel-lab \
      --query 'repositories[0].repositoryUri' \
      --output text \
      --region ${var.aws_region})

    docker pull $ECR_URI:latest
    docker run -d -p 8000:80 $ECR_URI:latest
  EOF

  tags = {
    Name        = "devsecops-lab-ec2"
    Environment = var.environment
  }
}

# after terraform apply these show me the EC2 IP and ECR URL

output "ec2_public_ip" {
  value = aws_instance.devsecops_lab.public_ip
}

output "ecr_repository_url" {
  value = aws_ecr_repository.laravel_app.repository_url
}