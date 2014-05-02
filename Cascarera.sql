-- phpMyAdmin SQL Dump
-- version 2.8.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Nov 08, 2007 at 07:13 PM
-- Server version: 5.0.32
-- PHP Version: 5.2.0-8+etch7
-- 
-- Database: `Cascarera`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `Contacto`
-- 

CREATE TABLE `Contacto` (
  `id` int(11) NOT NULL auto_increment,
  `Nombre` varchar(30) NOT NULL,
  `Apellido` varchar(30) NOT NULL,
  `Telefono` varchar(20) default NULL,
  `Nota` text,
  `Fecha_ingreso` datetime NOT NULL,
  `Fecha_nacimiento` date NOT NULL,
  `email` varchar(70) NOT NULL,
  `Genero` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `Contacto`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Genero`
-- 

CREATE TABLE `Genero` (
  `id` smallint(6) NOT NULL auto_increment,
  `Descripcion` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `Genero`
-- 

INSERT INTO `Genero` (`id`, `Descripcion`) VALUES (1, 'Masculino'),
(2, 'Femenino');
