Sample eshop
=================
1. Create a local.neon from local.neon.dist

2. Run me with docker compose up in the project root.

3. RUn composer install inside of container or via docker exec

Projects is then accessible on locahost port 80.

Sample database and data are created by calling url: /product/create

Created orders can be checked by calling url: /order/list

CS and PHPSTAN could be called by composer cs-check and composer phpstan inside container or with help of docker exec.