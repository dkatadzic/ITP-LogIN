## Info

**Springboot Clients können jeden Access Type haben (Public, Bearer Type oder Confidential), die Entscheidung welcher verwendet werden soll, muss für jede Anwendung evaluiert werden. Für die Demo Anwendung wurde ein Public Type benutzt.**

Um die Demo Anwendung zu erstellen haben wir folgendes Tutorial benutzt: [Tutorial](https://www.thomasvitale.com/spring-security-keycloak/) &[GitHub Code](https://github.com/ThomasVitale/spring-keycloak-tutorials/tree/master/keycloak-spring-security-first-look)
Viele der hier verwendeten Sachen stehen auch in der [Keycloak Dokumentation](https://www.keycloak.org/docs/latest/securing_apps/#java-adapters)

##  Java Springboot Demo

#### application.properties
In der Datei *src/main/resources/application.properties* werden die Daten von dem Keycloak Server angegeben. **Hierbei ist es wichtig, dass der Eintrag "keycloak.ssl-required" auf all gesetzt ist, wenn die Demo/Anwendung Online erreichbar ist.** Des Weiteren müssen die Werte der Datei auf die eigene Anwendung geändert werden bzw. auf die richtigen Werte geändert werden. Ein ungefährer Aufbau solcher Datei für ein Public Client:

````
keycloak.realm=<realm-name>
keycloak.resource=<client-id>
keycloak.auth-server-url=http:/<IP-Adresse>:8080/auth
keycloak.ssl-required=extern
keycloak.public-client=true
keycloak.principal-attribute=preferred_username

#Festlegen auf welchen Port die Anwendung laufen soll
server.port=8081

````

#### Dependencies (build.gradle)

Für die Anwendung werden folgenden Packages benötigt:

* spring-boot-starter-web
* spring-boot-starter-thymeleaf
* spring-boot-starter-security
* keycloak-spring-boot-starter

Unsere build.gradle Datei sah so aus: (Bei der Zeile `set('keycloakVersion', '11.0.3')` ist es wichtig die richtige Keycloak Version anzugeben)

````
buildscript {
    ext {
        springBootVersion = '2.1.9.RELEASE'
    }
    repositories {
        mavenCentral()
    }
    dependencies {
        classpath("org.springframework.boot:spring-boot-gradle-plugin:${springBootVersion}")
    }
}

plugins {
    id 'org.springframework.boot' version '2.4.1'
    id 'io.spring.dependency-management' version '1.0.10.RELEASE'
    id 'java'
}

group = 'Java'
version = '0.0.1-SNAPSHOT'
sourceCompatibility = '11'

repositories {
    mavenCentral()
}

ext {
    set('keycloakVersion', '11.0.3')
}

dependencies {
    // Spring
    implementation 'org.springframework.boot:spring-boot-starter-web'
    implementation 'org.springframework.boot:spring-boot-starter-thymeleaf'
    implementation 'org.springframework.boot:spring-boot-starter-security'

    // Keycloak
    implementation 'org.keycloak:keycloak-spring-boot-starter'

    // Test
    testImplementation 'org.springframework.boot:spring-boot-starter-test'
    testImplementation 'org.springframework.boot:spring-security-test'
    testImplementation 'org.keycloak:keycloak-test-helper'
}

dependencyManagement {
    imports {
        mavenBom "org.keycloak.bom:keycloak-adapter-bom:${keycloakVersion}"
    }
}

test {
    useJUnitPlatform()
}

````



#### LibraryController.java

In dieser Klasse werden die REST-Schnittstelle definiert.

````java
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
````

#### SecurityConfig.java

Die wichtigste Methode der Klasse ist `protected void configure(HttpSecurity http) throws Exception`. In ihr wird festgelegt welche der REST-Schnittstellen geschützt werden sollen. Also für welche Schnittstelle man authentifiziert sein muss und des Weiteren legen wir fest dass für die Schnittstelle */lehrer* man die Rolle Lehrer als User benötigt.

````java
....
@KeycloakConfiguration
public class SecurityConfig extends KeycloakWebSecurityConfigurerAdapter {

    /**
     * Registers the KeycloakAuthenticationProvider with the authentication manager.
     *
     * Since Spring Security requires that role names start with "ROLE_",
     * a SimpleAuthorityMapper is used to instruct the KeycloakAuthenticationProvider
     * to insert the "ROLE_" prefix.
     *
     * e.g. Schueler -> ROLE_Schueler
     *
     * Should you prefer to have the role all in uppercase, you can instruct
     * the SimpleAuthorityMapper to convert it by calling:
     * {@code grantedAuthorityMapper.setConvertToUpperCase(true); }.
     * The result will be: Schueler -> ROLE_Schueler.
     */
    @Autowired
    public void configureGlobal(AuthenticationManagerBuilder auth) {
        SimpleAuthorityMapper grantedAuthorityMapper = new SimpleAuthorityMapper();
        grantedAuthorityMapper.setPrefix("ROLE_");

        KeycloakAuthenticationProvider keycloakAuthenticationProvider = keycloakAuthenticationProvider();
        keycloakAuthenticationProvider.setGrantedAuthoritiesMapper(grantedAuthorityMapper);
        auth.authenticationProvider(keycloakAuthenticationProvider);
    }

    /**
     * Defines the session authentication strategy.
     *
     * RegisterSessionAuthenticationStrategy is used because this is a public application
     * from the Keycloak point of view.
     */
    @Bean
    @Override
    protected SessionAuthenticationStrategy sessionAuthenticationStrategy() {
        return new RegisterSessionAuthenticationStrategy(new SessionRegistryImpl());
    }

    /**
     * Define an HttpSessionManager bean only if missing.
     *
     * This is necessary because since Spring Boot 2.1.0, spring.main.allow-bean-definition-overriding
     * is disabled by default.
     */
    @Bean
    @Override
    @ConditionalOnMissingBean(HttpSessionManager.class)
    protected HttpSessionManager httpSessionManager() {
        return new HttpSessionManager();
    }

    /**
     * Define security constraints for the application resources.
     */
    @Override
    protected void configure(HttpSecurity http) throws Exception {
        super.configure(http);
        http
                .authorizeRequests()
                .antMatchers("/user").hasAnyRole("Schueler", "Lehrer")
                .antMatchers("/lehrer").hasRole("Lehrer")
                .anyRequest().permitAll();
    }
}
````

