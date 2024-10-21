# projeto_git

# next updates
- prevenir injeções de código e sanitizando a variável diretamente da superglobal $_REQUEST.
- padronizar nomeclaturas
- updateUser - tirar o validation parameter e definilos pos intersection
- para adicionar um parenteses em parametros exclusivos deve usar expressao regular
ex: 
select * from teste where (nome ='xxx' or idade='yyy') and id <> :id

- Autoloading e Namespaces
Utilize namespaces para evitar conflitos de nomes e facilitar a organização do código.
Aproveite o PSR-4 autoloading para carregar automaticamente suas classes, evitando require ou include manuais.

- Testes Automatizados
Escreva testes para suas funções (usando PHPUnit, por exemplo) para garantir que o código funcione conforme esperado. Testes também ajudam na manutenção e refatoração.