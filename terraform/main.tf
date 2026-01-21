terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  region = "us-east-1"
}

//creating security group//

resource "aws_security_group" "devsecops_sg" {
  name        = "devsecops-lab-sg"
  description = "Security group for DevSecOps lab EC2"

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["72.196.29.58/32"]  
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "devsecops-lab-sg"
  }
}


//creating EC2 instance//

resource "aws_instance" "devsecops_lab" {
  ami           = "ami-0c02fb55956c7d316"  # Amazon Linux 2
  instance_type = "t3.micro"
  security_groups = [aws_security_group.devsecops_sg.name]

  tags = {
    Name        = "devsecops-lab-ec2"
    Environment = var.environment
  }
}


