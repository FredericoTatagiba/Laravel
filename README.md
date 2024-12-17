Basicamente esse pequeno projeto consiste na criação das rotas Back-End para:
- CRUD de usuario administrador e usuario comum (cliente);
- CRUD de produto;
- CRUD de pedido.

Caminhos onde se encontra o que foi programado:
- App/Models,
- App/Http/Controllers,
- App/Http/Middleware,
- App/Http/Kernel.php,
- Config/auth.php,
- Config/app.php,
- Database/Migrations,
- Routes/api.php

O arquivo exportado para o Postman também foi adicionado para facilitar o teste:
- TesteCpaps.postman_collection

Observações:
1 - Tem de ser criado um administrador para que a criação de pedidos funcione.
2 - Só dá para testar a criação de pedidos quando logado ao usuario.
3 - O token de autenticação tem de ser colocado manualmente no Postman, pois eu não soube fazer um script para adcionar automaticamente.
