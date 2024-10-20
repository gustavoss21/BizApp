# projeto_git

# update
- updateUser - tirar o validation parameter e definilos pos intersection
- para adicionar um parenteses em parametros exclusivos deve usar expressao regular
ex: 
select * from teste where (nome ='xxx' or idade='yyy') and id <> :id

- evitar o acesso de outros arquivos como assets

- Autoloading e Namespaces
Utilize namespaces para evitar conflitos de nomes e facilitar a organização do código.
Aproveite o PSR-4 autoloading para carregar automaticamente suas classes, evitando require ou include manuais.

12. Testes Automatizados
Escreva testes para suas funções (usando PHPUnit, por exemplo) para garantir que o código funcione conforme esperado. Testes também ajudam na manutenção e refatoração.