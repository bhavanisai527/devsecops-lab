# AWS region where all my resources will be created

variable "aws_region" {
  description = "AWS region"
  type        = string
  default     = "us-east-1"
}

# EC2 instance size - t2.micro is free tier eligible

variable "instance_type" {
  description = "EC2 instance size"
  type        = string
  default     = "t3.micro"
}

# environment tag to identify my resources in AWS console

variable "environment" {
  description = "Deployment environment"
  type        = string
  default     = "lab"
}