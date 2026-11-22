-- Dados FICTÍCIOS opcionais para demonstração. Importe após o schema.sql.
USE bd_saul;

INSERT INTO estudante (nome, turma, ensino, telefone, condicao)
SELECT * FROM (
  SELECT 'Aluno Exemplo 1' AS nome, '7A' AS turma, 'Fundamental II' AS ensino, '' AS telefone, 'Ativo' AS condicao
  UNION ALL SELECT 'Aluno Exemplo 2', '1B', 'Ensino Médio', '', 'Ativo'
  UNION ALL SELECT 'Aluno Exemplo 3', '5C', 'Fundamental I', '', 'Ativo'
) t WHERE NOT EXISTS (SELECT 1 FROM estudante WHERE nome LIKE 'Aluno Exemplo %');

INSERT INTO livro (titulo, autor, editora, materia, isbn, quantidade)
SELECT * FROM (
  SELECT 'Dom Casmurro' AS titulo, 'Machado de Assis' AS autor, 'Editora Exemplo' AS editora, 'Literatura' AS materia, '' AS isbn, 3 AS quantidade
  UNION ALL SELECT 'O Cortiço', 'Aluísio Azevedo', 'Editora Exemplo', 'Literatura', '', 2
  UNION ALL SELECT 'Matemática Básica', 'Autor Exemplo', 'Editora Exemplo', 'Matemática', '', 4
  UNION ALL SELECT 'Atlas Geográfico', 'Autor Exemplo', 'Editora Exemplo', 'Geografia', '', 1
) t WHERE NOT EXISTS (SELECT 1 FROM livro WHERE editora = 'Editora Exemplo');

INSERT INTO emprestimo (idlivro, idestudante, data_emprestimo, data_prevista, data_devolucao)
SELECT l.idlivro, e.idestudante, CURDATE() - INTERVAL 20 DAY, CURDATE() - INTERVAL 6 DAY, NULL
FROM livro l JOIN estudante e ON e.nome = 'Aluno Exemplo 1'
WHERE l.titulo = 'Dom Casmurro' AND NOT EXISTS (SELECT 1 FROM emprestimo);

INSERT INTO emprestimo (idlivro, idestudante, data_emprestimo, data_prevista, data_devolucao)
SELECT l.idlivro, e.idestudante, CURDATE() - INTERVAL 40 DAY, CURDATE() - INTERVAL 26 DAY, CURDATE() - INTERVAL 28 DAY
FROM livro l JOIN estudante e ON e.nome = 'Aluno Exemplo 2'
WHERE l.titulo = 'O Cortiço' AND (SELECT COUNT(*) FROM emprestimo) = 1;

INSERT INTO emprestimo (idlivro, idestudante, data_emprestimo, data_prevista, data_devolucao)
SELECT l.idlivro, e.idestudante, CURDATE() - INTERVAL 2 DAY, CURDATE() + INTERVAL 12 DAY, NULL
FROM livro l JOIN estudante e ON e.nome = 'Aluno Exemplo 3'
WHERE l.titulo = 'Matemática Básica' AND (SELECT COUNT(*) FROM emprestimo) = 2;
