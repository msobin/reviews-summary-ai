[![Contributors](https://img.shields.io/github/contributors/msobin/review-summary-ai.svg?style=for-the-badge)](https://github.com/msobin/review-summary-ai/graphs/contributors)
[![Forks](https://img.shields.io/github/forks/msobin/review-summary-ai.svg?style=for-the-badge)](https://github.com/msobin/review-summary-ai/network/members)
[![Stargazers](https://img.shields.io/github/stars/msobin/review-summary-ai.svg?style=for-the-badge)](https://github.com/msobin/review-summary-ai/stargazers)
[![Issues](https://img.shields.io/github/issues/msobin/review-summary-ai.svg?style=for-the-badge)](https://img.shields.io/github/issues/msobin/review-summary-ai.svg?style=for-the-badge)
[![MIT License](https://img.shields.io/github/license/msobin/review-summary-ai.svg?style=for-the-badge)]( https://github.com/msobin/review-summary-ai/blob/master/LICENSE.txt)
[![LinkedIn](https://img.shields.io/badge/linkedin-%230077B5.svg?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/maximsobin)

## About The Project
This Telegram bot allows users to submit links to products from various online marketplaces. It extracts and analyzes customer reviews to generate concise AI summaries based on configurable prompts. The summaries highlight key points like product pros and cons, overall satisfaction, and specific features, helping users make informed purchasing decisions. The bot's configuration can be tailored to focus on different aspects according to product type or user preference.

*The project is still in development and some features may not be fully implemented.*

### Built With
* PHP (Symfony)
* Redis
* RabbitMQ
* Docker

### Requirements
* [Docker](https://www.docker.com/)
* Docker-compose (comes with Docker)
* [Task](https://taskfile.dev/) (task runner)

### Installation

Clone the repo
   ```sh
    git clone git@github.com:msobin/review-summary-ai.git
   ```
Run the following command in project directory to start the project
   ```sh
    task up
   ```

*The ports used can be overridden by creating a docker-compose.override.yaml file with the following contents:*
```yaml
version: '3'

services:
  nginx:
    ports: !override
      - "8080:80"
  rabbitmq:
    ports: !override
      - "15673:15672"
```

### Usage


