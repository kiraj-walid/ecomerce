-- Script SQL pour la boutique en ligne

CREATE DATABASE IF NOT EXISTS boutique;
USE boutique;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255),
    role ENUM('admin', 'client') DEFAULT 'client'
);

-- Table des produits
CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT,
    prix DECIMAL(10,2),
    categorie VARCHAR(50),
    image VARCHAR(255),
    stock INT DEFAULT 0
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50),
    mode_paiement VARCHAR(50),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table des lignes de commande
CREATE TABLE IF NOT EXISTS lignes_commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT,
    produit_id INT,
    quantite INT,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Table du panier (optionnel, sinon stocker en session)
CREATE TABLE IF NOT EXISTS paniers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    produit_id INT,
    quantite INT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Table des images des produits
CREATE TABLE IF NOT EXISTS images_produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT,
    image VARCHAR(255),
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- Insertion des produits Phone Accessories
INSERT INTO produits (nom, prix, description, categorie) VALUES
('Silicone Phone Case', 39, 'Ultra-thin, shockproof, multiple colors', 'Phone Accessories'),
('Wireless Earbuds', 129, 'Bluetooth 5.0, 4h battery life', 'Phone Accessories'),
('Magnetic Phone Holder', 49, 'For car or desk, 360° rotation', 'Phone Accessories'),
('Fast Charging Cable (Type-C)', 29, 'Braided, durable, 1.5m length', 'Phone Accessories');

-- Insertion des produits Laptop Accessories
INSERT INTO produits (nom, prix, description, categorie) VALUES
('Laptop Cooling Pad', 139, '5 silent fans, adjustable tilt angles', 'Laptop Accessories'),
('Wireless Mouse', 79, 'Compact, USB receiver included', 'Laptop Accessories'),
('Laptop Stand Adjustable', 99, 'Foldable aluminum design', 'Laptop Accessories');

-- Insertion des produits USB & Storage
INSERT INTO produits (nom, prix, description, categorie) VALUES
('USB Drive 64GB', 75, 'USB 3.0, metal body', 'USB & Storage'),
('External HDD 1TB', 499, 'Fast data transfer, shock resistant', 'USB & Storage');

-- Insertion des produits Desk Gadgets
INSERT INTO produits (nom, prix, description, categorie) VALUES
('LED Desk Lamp w/ USB Port', 169, 'Adjustable light, night mode', 'Desk Gadgets'),
('Mini Desk Fan USB', 89, 'Quiet, portable, plug & play', 'Desk Gadgets'),
('Digital Alarm Clock', 99, 'LED display, temperature sensor', 'Desk Gadgets');

-- Insertion des produits Wearable Tech
INSERT INTO produits (nom, prix, description, categorie) VALUES
('Smartwatch Student Edition', 299, 'Step counter, notifications, sleep track', 'Wearable Tech'),
('Fitness Tracker Band', 199, 'Waterproof, long battery life', 'Wearable Tech'); 