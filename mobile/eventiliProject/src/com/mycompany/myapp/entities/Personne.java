/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities;

/**
 *
 * @author msi
 */
public class Personne {

    private int id_pers;
    private String nom_pers, prenom_pers, email, adresse, image, rib, num_tel;
    
    private String role;

    public Personne() {
    }

    public Personne(int id_pers, String nom_pers, String prenom_pers, String num_tel, String email, String adresse, String image, String rib,String role) {
        this.id_pers = id_pers;
        this.nom_pers = nom_pers;
        this.prenom_pers = prenom_pers;
        this.num_tel = num_tel;
        this.email = email;
        this.adresse = adresse;
        this.image = image;
        this.rib = rib;
        this.role=role;
    }

    public int getId_pers() {
        return id_pers;
    }

    public String getNum_tel() {
        return num_tel;
    }

    public String getNom_pers() {
        return nom_pers;
    }

    public String getPrenom_pers() {
        return prenom_pers;
    }

    public String getEmail() {
        return email;
    }

    public String getAdresse() {
        return adresse;
    }

    public String getImage() {
        return image;
    }

    public String getRib() {
        return rib;
    }

    public void setNum_tel(String num_tel) {
        this.num_tel = num_tel;
    }

    public void setNom_pers(String nom_pers) {
        this.nom_pers = nom_pers;
    }

    public void setPrenom_pers(String prenom_pers) {
        this.prenom_pers = prenom_pers;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public void setAdresse(String adresse) {
        this.adresse = adresse;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public void setRib(String rib) {
        this.rib = rib;
    }

    public String getRole() {
        return role;
    }

    public void setRole(String role) {
        this.role = role;
    }

    public void setId_pers(int id_pers) {
        this.id_pers = id_pers;
    }

    
    
    @Override
    public String toString() {
        return "Personne{" + "id_pers=" + id_pers + ", nom_pers=" + nom_pers + ", prenom_pers=" + prenom_pers + ", email=" + email + ", adresse=" + adresse + ", image=" + image + ", rib=" + rib + ", num_tel=" + num_tel + ", role=" +this.role+'}';
    }

}