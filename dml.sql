CREATE DATABASE kanban_db;
USE kanban_db;

CREATE TABLE task_lists (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            title VARCHAR(255) NOT NULL
);

CREATE TABLE cards (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       task_list_id INT NOT NULL,
                       title VARCHAR(255) NOT NULL,
                       position INT DEFAULT 0,
                       FOREIGN KEY (task_list_id) REFERENCES task_lists(id)
);

-- Inserindo dados de teste
INSERT INTO task_lists (title) VALUES ('A Fazer'), ('Em Andamento'), ('Concluído');
INSERT INTO cards (task_list_id, title, position) VALUES (1, 'Estudar PHP', 0), (1, 'Comprar Café', 1), (2, 'Criar Banco', 0);