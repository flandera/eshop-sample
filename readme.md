Sample eshop
=================
1. Create a local.neon from local.neon.dist

2. Run me with docker compose up in the project root.

Projects is then accessible on locahost port 80.

Sample database and data are created by calling url: /product/create

Created orders can be checked by calling url: /cart/orders

CS and PHPSTAN could be called by composer cs-check and composer phpstan inside container or with help of docker exec.