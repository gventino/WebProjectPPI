# WebProjectPPI

Repositório dedicado ao trabalho final da disciplina Programação para Internet do Bacharelado em Ciências da Computação UFU.

### Primeiro passo:

Execute:

`cp env.example .env`

### Para executar basta rodar:

`sudo docker compose up -d --build`

O docker compose vai subir dois containers, um para o mysql e um para a aplicacao php. Qualquer ao ser atualizado atualiza o container php_app automaticamente (mapeei com volumes), sendo desnecessario o rebuild manual do container.

### Caso queira usar o bash do container:

`sudo docker exec -it php_app /bin/bash`

### Para acessar as paginas:

Foi configurado um container com nginx para servir as paginas html. Para acessar elas, basta ir em:

`http://localhost:8080/front/...`

Depois podemos reconfigurar o nginx para mapear sem o `/front/...`, e mapear direto a pagina inicial.
