package logindemo.controller;

import logindemo.repository.BookRepository;
import org.keycloak.KeycloakSecurityContext;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;

@Controller
public class LibraryController {
    private final HttpServletRequest request;
    private final BookRepository bookRepository;

    @Autowired
    public LibraryController(HttpServletRequest request, BookRepository bookRepository) {
        this.request = request;
        this.bookRepository = bookRepository;
    }

    @GetMapping(value = "/")
    public String getHome() {
        return "index";
    }

    @GetMapping(value = "/user")
    public String getBooks(Model model) {
        configCommonAttributes(model);
        model.addAttribute("books", bookRepository.readAll());
        return "user";
    }



    @GetMapping(value = "/lehrer")
    public String getManager(Model model) {
        configCommonAttributes(model);
        model.addAttribute("books", bookRepository.readAll());
        return "lehrer";
    }

    @GetMapping(value = "/logout")
    public String logout() throws ServletException {
        request.logout();
        return "redirect:/";
    }

    private void configCommonAttributes(Model model) {
        model.addAttribute("name", getKeycloakSecurityContext().getIdToken().getPreferredUsername());
    }

    /**
     * The KeycloakSecurityContext provides access to several pieces of information
     * contained in the security token, such as user profile information.
     */
    private KeycloakSecurityContext getKeycloakSecurityContext() {
        return (KeycloakSecurityContext) request.getAttribute(KeycloakSecurityContext.class.getName());
    }
}